<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use App\Models\MediaTeam;
use App\Models\BranchRelated;
use App\Models\Branch;
use App\Models\Media;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
 
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function addUser(Request $request)
    {
        if($request->input('id'))
            $user = User::find($request->input('id'));
            else
            $user = new User();
        
        $validator = Validator::make(
            $request->all(),
            [
                'name'     => 'required|string|between:2,100',
                'email'    => 'required|email|unique:users,email,'.$user->id.',id',
                'emp_code'    => 'required|string|unique:users,emp_code,'.$user->id.',id',
                'role_id'     =>'required',
                'branch_id' =>'required',
                'supervisor_id' =>'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
      
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        $user->emp_code = $request->input("emp_code");
        $user->role_id = $request->input("role_id");
        $user->role_id = $request->input("role_id");
        $user->supervisor_id =$request->input("supervisor_id");
        $user->team_id =$request->input("team_id");
        $user->password =bcrypt("password");
        $user->save();      
        $this->saveRelatedBranch($user,$request);
        return response()->json(['message' => 'User created successfully', 'user' => $user]);

    }

    public function saveRelatedBranch($user,$request)
    {
        DB::table('branch_related')->where('user_id', $user->id)->delete();
        $branchIds =   $request->input('branch_id');
        $relatedBranch=array();
        foreach($branchIds as $key=>$branchId)
        {
              $relatedBranch[]=array('user_id'=>$user->id,'branch_id'=>$branchId);
        }
        DB::table('branch_related')->insert($relatedBranch);
    }

    public function userList(Request $request)
    {
            $branchquery = '';
            $role = Role::where('parent_id',auth()->user()->role_id)->get();
            $roleId = array();
            foreach($role as $role)
            {
                    $roleId[] = $role->id;
            }
            $branchs = BranchRelated::where('user_id',auth()->user()->id)->get();
            $branchId = array();           
            foreach($branchs as $branch)
            {
                $branchId[] = $branch->branch_id;
            }           
            $roleId = implode(',',array_unique($roleId));
            $branchId = implode(',',array_unique($branchId));
            $term = $request->input('term');
            $searchfieldName = $request->input('searchfieldName');
            if(auth()->user()->id !=1)
            $select = 'users.*,branch_related.*,media_team.team_name as team_name';
            else
            $select = 'users.*,media_team.team_name as team_name';
            $query = DB::table('users')->select(DB::raw($select));
            $query->leftJoin('media_team', 'media_team.id', '=', 'users.team_id');
            $query->where('users.id','!=',auth()->user()->id)->where('users.status',1);
            if(auth()->user()->id !=1)
            {
                $query->leftJoin('branch_related', 'branch_related.user_id', '=', 'users.id');
            if($roleId != '')
                $branchquery = "users.role_id in ($roleId) AND";   
            $query->where('users.id','!=',auth()->user()->supervisor_id);       
            
            $query->whereRaw("$branchquery branch_related.branch_id in ($branchId) GROUP by users.id");
            }
            if($term !=null && $term !='' && $searchfieldName)
            $query = $query->Where($searchfieldName, 'LIKE', '%'.$term.'%');        
            $query->orderBy($request->input('orderBy'), $request->input('order'));
           
            $pageSize = $request->input('pageSize');
            $data = $query->paginate($pageSize,['*'],'page_no');
            $results = $data->items();
            $count = $data->total();
            if(!empty($results) && count($results) >0){
                foreach($results as $result){
                    $result->role_name  = ($result->role_id == null)?null:$this->_getRoleName($result->role_id);
                    $result->supervisor_name = $this->_getUserName($result->supervisor_id);
                    $tmp  = $this->_getBranchName($result->id);
                    $result->branch_id = $tmp['branch_id'];
                    $result->branch_name = $tmp['branch_name'];
                }
            }
            $data = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $count,
            "data" => $results
            ];
            return json_encode($data);    
       
    }

    function _getUserName($id)
    {
        $user = User::find($id);
        if($user !=null)
        return $user->name;
        return '';
    }

    function _getRoleName($id)
    {
        $role = Role::find($id);
        if($role !=null)
        return $role->role_name;
        return '';
    }

    function _getBranchName($id)
    {
        $tmp = array('branch_id'=>array(),'branch_name'=>array());
        $query = BranchRelated::select();
        $query = $query->where('branch_related.user_id',$id);
        $query = $query->leftJoin("branch", "branch.id", "=", "branch_related.branch_id");
        $datas = $query->get();
        foreach($datas as $data)
        {
            $tmp['branch_id'][] = $data->branch_id;
            $tmp['branch_name'][] = $data->branch_name;
        }
        return $tmp;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        $user->status = 0;
        $user->save();
        return response()->json(['message' => 'User Deleted successfully', 'user' => $user]);
    }

    public function getRole()
    {
        $user = Auth::user();
        $role = Role::find($user->role_id);
        return response()->json($role);
    }

    public function getTeam()
    {
        $team = MediaTeam::all();
        return response()->json($team);
    }

    public function getSupervisor($roleID,$branchID)
    {
        if($roleID != "null" && $roleID != 'undefined' && $branchID == "null")
        {
             $role = Role::find($roleID);
             $users = User::where('role_id',$role->parent_id)->get();
             return response()->json($users);
        }
        else if($roleID != "null" && $branchID !="null" )
        {
            $role = Role::find($roleID);
            $branchID = explode(',',$branchID);
            $branchs = BranchRelated::whereIn('branch_id',$branchID)->get();
            $userId = array();
            foreach($branchs as $branch)
            {
                $userId[] = $branch->user_id;
            }
            $userId = array_unique($userId);
            $users = User::where('role_id',$role->parent_id)->whereIn('id',$userId)->get();
            return response()->json($users);
        }
   
    }

   

    public function getcountWait()
    {
        $branchId = $this->_getBranchId();
        $branches = Branch::whereIn('id',$branchId)->get();
        $result = array();
        $media_query = "SELECT COUNT(id) AS count_id FROM media WHERE recovery_possibility = 'Yes'";
        foreach($branches as $branch){
            $result[$branch->branch_name]=array("branch_id"=>$branch->id,"stage_id"=>7);
            $data = DB::select($media_query." AND branch_id = ".(int)$branch->id."  AND stage = 7");
            $result[$branch->branch_name]['totalMedia']=$data[0]->count_id;            
        }
        return $result;
    }

    public function dashbordConfirm($stage_id)
    {
        $branchId = $this->_getBranchId();
        $branches = Branch::whereIn('id',$branchId)->get();
        $result = array();
        $media_query = "SELECT COUNT(id) AS count_id FROM media WHERE 1";
        foreach($branches as $branch){
            $result[$branch->branch_name]=array("branch_id"=>$branch->id,"stage_id"=>$stage_id);
            $data = DB::select($media_query." AND branch_id = ".(int)$branch->id."  AND stage = ".$stage_id."");
            $result[$branch->branch_name]['totalMedia']=$data[0]->count_id;            
        }
        return $result;
    }

    public function DashBaordCount()
    {
        return ['Pre'=>$this->dashbordCount(1),'PreDone'=>$this->dashbordCount(3),'MediaIn'=>$this->dashbordCount(4),
                'AssInProcess'=>$this->dashbordCount(5),'AssInDone'=>$this->dashbordCount(6),'casePossible'=>$this->dashbordCount(7,'Yes'),
                'caseNotPossible'=>$this->dashbordCount(8,'No'),'countConfirm'=>$this->dashbordConfirm(8),'countNotConfirm'=>$this->dashbordConfirm(9),
                'countWait'=>$this->getcountWait()];
    }

    public function dashbordCount($stage_id='',$recovery_possibility=''){
        $branchId = $this->_getBranchId();
        $branches = Branch::whereIn('id',$branchId)->get();
        $result = array();
        if(isset($stage_id) && $stage_id !=''){
            $media_query = "SELECT COUNT(id) AS count_id FROM media WHERE 1";
            $transfer_query = "SELECT COUNT(tm.new_branch_id) AS count_id FROM transfer_media AS tm LEFT JOIN media AS m ON tm.id = m.transfer_id WHERE 1";
            foreach($branches as $branch){

                $result[$branch->branch_name]=array("branch_id"=>$branch->id,"stage_id"=>$stage_id,"user_id"=>auth()->user()->id);
             
                if($stage_id==1){
                    $media_stage = " AND stage IN (1,2)";
                    $transfer_stage = " AND m.stage IN (1,2)";
                    $media_month = "";
                    $transfer_month = "";
                }
                elseif($stage_id==3 || $stage_id==5 || $stage_id==6){
                    $media_stage = " AND stage = ".(int)$stage_id."";
                    $transfer_stage = " AND m.stage = ".(int)$stage_id."";
                    $media_month = "";
                    $transfer_month = "";
                } 
                elseif($stage_id==4){
                    $media_stage = " AND stage NOT IN (1,2,3)";
                    $transfer_stage = " AND m.stage NOT IN (1,2,3)";
                    $media_month = " AND created_on >= DATE_FORMAT(NOW(), '%Y-%m-01')";
                    $transfer_month = " AND m.created_on >= DATE_FORMAT(NOW(), '%Y-%m-01')";
                } elseif($stage_id==7 && $recovery_possibility!=''){
                    $media_stage = " AND stage = 6 AND recovery_possibility ='".$recovery_possibility."' ";
                    $transfer_stage = " AND m.stage = 6 AND m.recovery_possibility ='".$recovery_possibility."'";
                    $media_month = "";
                    $transfer_month = "";
                } elseif($stage_id==8 && $recovery_possibility!=''){
                    $media_stage = " AND stage = 6 AND recovery_possibility ='".$recovery_possibility."'";
                    $transfer_stage = " AND m.stage = 6 AND m.recovery_possibility ='".$recovery_possibility."'";
                    $media_month = "";
                    $transfer_month = "";
                }
                // Assigned Count 
                $assigned_media_query = $media_query." AND branch_id = ".(int)$branch->id." ".$media_stage." AND user_id is not null AND transfer_id is null ".$media_month."";
                $assigned_transfer_query = $transfer_query." ".$transfer_stage." AND tm.new_branch_id = ".(int)$branch->id." AND m.user_id is not null AND m.transfer_id is not null ".$transfer_month."";             
                $assigned_media_result = DB::select($assigned_media_query);
                $assigned_transfer_result = DB::select($assigned_transfer_query);
                $assigned_count_result = ($assigned_media_result[0]->count_id + $assigned_transfer_result[0]->count_id);
                $result[$branch->branch_name]['assigned']=($assigned_count_result == 0)?'':$assigned_count_result;
                // Unassigned Count
                $unassigned_media_query = $media_query." AND branch_id = ".(int)$branch->id." ".$media_stage." AND user_id is null AND transfer_id is null ".$media_month."";
                $unassigned_transfer_query = $transfer_query." ".$transfer_stage." AND tm.new_branch_id = ".(int)$branch->id." AND m.user_id is null AND m.transfer_id is not null ".$transfer_month."";             
                $unassigned_media_result = DB::select($unassigned_media_query);
                $unassigned_transfer_result = DB::select($unassigned_transfer_query);
                $unassigned_count_result = ($unassigned_media_result[0]->count_id + $unassigned_transfer_result[0]->count_id);
                $result[$branch->branch_name]['unasigned']=($unassigned_count_result == 0)?'':$unassigned_count_result;
                // Total Count 
                $total_count_result = $assigned_count_result + $unassigned_count_result;
                $result[$branch->branch_name]['total']=($total_count_result == 0)?'':$total_count_result;
                // My Assigned Count 
                $my_assigned_media_query = $media_query." AND branch_id = ".(int)$branch->id." ".$media_stage." AND user_id = ".(int)auth()->user()->id." AND transfer_id is null ".$media_month."";
                $my_assigned_transfer_query = $transfer_query." ".$transfer_stage." AND tm.new_branch_id = ".(int)$branch->id." AND m.user_id = ".(int)auth()->user()->id." AND m.transfer_id is not null ".$transfer_month."";             
                $my_assigned_media_result = DB::select($my_assigned_media_query);
                $my_assigned_transfer_result = DB::select($my_assigned_transfer_query);
                $my_assigned_count_result = ($my_assigned_media_result[0]->count_id + $my_assigned_transfer_result[0]->count_id);
                $result[$branch->branch_name]['my_assigned']=($my_assigned_count_result == 0)?'':$my_assigned_count_result;
            }
        }
        return  $result;
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        if(!Hash::check($request->old_password, auth()->user()->password)){
            return response()->json(['password'=>"Old Password Not Match"], 400);
         }
        else
        {
            User::whereId(auth()->user()->id)->update([
                'password' =>bcrypt($request->new_password)
            ]);
            $token = JWTAuth::getToken();
            JWTAuth::setToken($token)->invalidate();
            return response()->json(['password'=>"Password has been Changed"]);
        }
    }

}