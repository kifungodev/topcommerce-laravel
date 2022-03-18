<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use Illuminate\Http\Request;
use Image;
use File;
class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $campaigns = Campaign::all();
        return view('admin.campaign',compact('campaigns'));
    }


    public function create()
    {
        return view('admin.create_campaign');
    }


    public function store(Request $request)
    {
        $rules = [
            'name'=>'required|unique:campaigns',
            'slug'=>'required|unique:campaigns',
            'image'=>'required',
            'title'=>'required',
            'offer'=>'required|numeric',
            'start_date'=>'required|date',
            'end_date'=>'required|date',
            'status'=>'required',
            'show_homepage'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'name.unique' => trans('admin_validation.Name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'image.required' => trans('admin_validation.Image is required'),
            'title.required' => trans('admin_validation.Title is required'),
            'offer.required' => trans('admin_validation.Offer is required'),
            'start_date.required' => trans('admin_validation.Start date is required'),
            'end_date.required' => trans('admin_validation.End date is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'show_homepage.required' => trans('admin_validation.Show homepage is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $campaign = new Campaign();
        if($request->image){
            $extention=$request->image->getClientOriginalExtension();
            $image_name = 'campaign-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name ='uploads/custom-images/'.$image_name;
            Image::make($request->image)
                ->save(public_path().'/'.$image_name);
            $campaign->image = $image_name;
        }

        $campaign->name = $request->name;
        $campaign->slug = $request->slug;
        $campaign->title = $request->title;
        $campaign->offer = $request->offer;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->status = $request->status;
        $campaign->show_homepage = $request->show_homepage;
        $campaign->save();

        if($request->show_homepage == 1){
            Campaign::where('id','!=',$campaign->id)->update(['show_homepage' => 0]);
        }

        $notification=trans('admin_validation.Created Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.campaign.index')->with($notification);
    }

    public function edit($id)
    {
        $campaign = Campaign::find($id);
        return view('admin.edit_campaign',compact('campaign'));
    }


    public function update(Request $request,$id)
    {
        $campaign = Campaign::find($id);
        $rules = [
            'name'=>'required|unique:campaigns,name,'.$campaign->id,
            'slug'=>'required|unique:campaigns,slug,'.$campaign->id,
            'title'=>'required',
            'offer'=>'required|numeric',
            'start_date'=>'required|date',
            'end_date'=>'required|date',
            'status'=>'required',
            'show_homepage'=>'required',
        ];
        $customMessages = [
            'name.required' => trans('admin_validation.Name is required'),
            'name.unique' => trans('admin_validation.Name already exist'),
            'slug.required' => trans('admin_validation.Slug is required'),
            'slug.unique' => trans('admin_validation.Slug already exist'),
            'image.required' => trans('admin_validation.Image is required'),
            'title.required' => trans('admin_validation.Title is required'),
            'offer.required' => trans('admin_validation.Offer is required'),
            'start_date.required' => trans('admin_validation.Start date is required'),
            'end_date.required' => trans('admin_validation.End date is required'),
            'status.required' => trans('admin_validation.Status is required'),
            'show_homepage.required' => trans('admin_validation.Show homepage is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        if($request->image){
            $old_image = $campaign->image;
            $extention=$request->image->getClientOriginalExtension();
            $image_name = 'campaign-'.date('-Y-m-d-h-i-s-').rand(999,9999).'.'.$extention;
            $image_name ='uploads/custom-images/'.$image_name;
            Image::make($request->image)
                ->save(public_path().'/'.$image_name);
            $campaign->image = $image_name;
            $campaign->save();
            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }
        }

        $campaign->name = $request->name;
        $campaign->slug = $request->slug;
        $campaign->title = $request->title;
        $campaign->offer = $request->offer;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->status = $request->status;
        $campaign->show_homepage = $request->show_homepage;
        $campaign->save();

        if($request->show_homepage == 1){
            Campaign::where('id','!=',$campaign->id)->update(['show_homepage' => 0]);
        }

        $notification=trans('admin_validation.Update Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.campaign.index')->with($notification);
    }

    public function destroy($id)
    {
        $campaign = Campaign::find($id);
        $old_image = $campaign->image;
        $campaign->delete();
        if($old_image){
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }
        CampaignProduct::where('campaign_id', $id)->delete();

        $notification=trans('admin_validation.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.campaign.index')->with($notification);
    }

    public function changeStatus($id){
        $campaign = Campaign::find($id);
        if($campaign->status==1){
            $campaign->status=0;
            $campaign->save();
            $message= trans('admin_validation.Inactive Successfully');
        }else{
            $campaign->status=1;
            $campaign->save();
            $message= trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
