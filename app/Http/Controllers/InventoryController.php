<?php
namespace App\Http\Controllers;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function inventoryList(Request $request)
    {
        $filter = $request->input('filter');
        $query = DB::table('inventory')->select(DB::raw('inventory.*'));
        foreach($filter as $key=>$value)
        {
            if($value != null)
                $query->where($key,'=',$value);
        }
        $query->orderBy($request->input('orderBy'), $request->input('order'));
        $pageSize = $request->input('pageSize');
        $data = $query->paginate($pageSize,['*'],'page_no');
        $results = $data->items();
        $count = $data->total();
        $data = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $count,
            "data" => $results,
            "test"=>$query
            ];
    return json_encode($data);
    }

    public function inventorySave(Request $request)
    {
        if($request->input('id'))
        $inventory = Inventory::find($request->input('id'));
        else
        $inventory = new Inventory();

        $validator = Validator::make(
            $request->all(),
            [
                'serial_num' => 'required|string|unique:inventory,serial_num,'.$inventory->id.',id'
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $inventory->model_num = $request->input("model_num");
        $inventory->serial_num = $request->input("serial_num");
        $inventory->pcb_num = $request->input("pcb_num");
        $inventory->interface = $request->input("interface");
        $inventory->firmware = $request->input("firmware");
        $inventory->date_purchase = $request->input("date_purchase");
        $inventory->capacity = $request->input("numsize")." ".$request->input("size");
        $inventory->type = $request->input("type");
        $inventory->rack_num = $request->input("rack_num");
        $inventory->inventory_type = $request->input("inventory_type");
        $inventory->received_from = $request->input("received_from");
        $inventory->branch_id = $request->input("branch_id");
        $inventory->job_id = $request->input("job_id");
        $inventory->remarks = $request->input("remarks");
        $inventory->save();
        return response()->json(['message' => 'Item created successfully', 'inventory' => $inventory]);

    }

    public function getInventory($id)
    {
        $inventory = Inventory::find($id);
        return response()->json($inventory);
    }
}