<?php

namespace App\Http\Controllers;
use App\Models\Branch;
use App\Models\BranchRelated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    protected $branch;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addBranch(Request $request)
    {

        $validator = Validator::make($request->all(),['branch_name'=> 'required|unique:branch','country_id'=>'required','state_name'=>'required','address'=>'required']);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $branch = Branch::create($validator->validated());
        return response()->json(['message' => 'Branch created successfully', 'role' => $branch]);
    }

    public function updateBranch(Request $request)
    {
        $validator = Validator::make($request->all(),['branch_name'=> 'required','country_id'=>'required','state_name'=>'required','address'=>'required']);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $branch = Branch::find($request->input('id'));
        $branch->branch_name = $request->input('branch_name');
        $branch->country_id = $request->input('country_id');
        $branch->state_name = $request->input('state_name');
        $branch->address = $request->input('address');
        $branch = $branch->save();
        return response()->json(['message' => 'Branch Update successfully', 'role' => $branch]);
    }

    public function branchList(Request $request)
    {
        $query = Branch::select('*')->leftJoin("country", "country.country_id", "=", "branch.country_id");
        return $this->_getPaginatedResult($query,$request);    
    }

    public function allBranch()
    {
        if(auth()->user()->id ==1)
        {
            $branchs = Branch::all();
        }
        else
        {
            $relatedBranch = BranchRelated::where('user_id',auth()->user()->id)->get();
            $branchId =array();
            foreach($relatedBranch as $branch)
            {
                $branchId[] = $branch->branch_id;
            }

            $branchs = Branch::whereIn('id',$branchId)->get();

        }
        return response()->json($branchs);
    }
}
