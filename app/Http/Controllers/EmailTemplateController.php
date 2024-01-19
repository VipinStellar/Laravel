<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\EmailTemplate;
use DB;
class EmailTemplateController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function templateList(Request $request){
        $nameData = trim($request->input('nameData'));

        $query = DB::table('email_template')->select(DB::raw('id,name,subject,status,created_at,updated_at'));
        if($nameData !='' && $nameData !=null){
            $query->Where('name', 'LIKE', '%'.$nameData.'%');
        }
        return $this->_getPaginatedResult($query,$request);
    }
    public function templateAdd(Request $request){
       $data = array();
        if($request->isMethod('post')){
             // validation
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'subject' => 'required',
                'template'=> 'required',
                'status'  => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $date = Carbon::now();
            if($request->input('id')!= '' && $request->input('id')!= null){
                $Email_temp = EmailTemplate::find($request->input('id'));
                $Email_temp->updated_at = $date->format('Y-m-d H:i:s');
                $data['message'] = 'Email Template Edit successfully';
            } else {
                $Email_temp = new EmailTemplate();
                $Email_temp->created_at = $date->format('Y-m-d H:i:s');
                $data['message'] = 'Email Template Add successfully';
            }
            $Email_temp->name = $request->input("name");
            $Email_temp->subject = $request->input("subject");
            $Email_temp->template = $request->input("template");
            $Email_temp->status = $request->input("status");
            $Email_temp->save();  
            
        }
        return response()->json($data);
    }
    public function templateDetail($id){
        $Template = EmailTemplate::find($id);
        return response()->json($Template);
    }
    public function deleteTemplate($id)
    {
        DB::table('email_template')->where('id', $id)->delete();
        return response()->json(['message' => 'Email Template Deleted Successfully!']);
    }
}