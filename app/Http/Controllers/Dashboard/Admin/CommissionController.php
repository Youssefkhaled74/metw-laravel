<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipmentCommission;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\VendorCommission;
use App\Models\ShipmentCompany;

class CommissionController extends Controller
{
    public function index()
    {
        return view('dashboard.admin.commissions.index', [
            'vendorCommission'   => VendorCommission::first(),
            'shipmentCommission' => ShipmentCommission::first(),
        ]);
    }

    public function storeVendor(Request $request)
    {
        VendorCommission::updateOrCreate(
            ['vendor_id' => null],
            $request->validate([
                'annual_subscription' => 'required|numeric',
                'order_commission_percent' => 'required|numeric',
                'order_commission_min' => 'required|numeric',
                'annual_target_commission' => 'required|numeric',
                // 'refund_fee_percent' => 'required|numeric',
                // 'refund_fee_min' => 'required|numeric',
            ])
        );

        return back()->with('success', 'Vendor commission saved successfully');
    }

    public function storeShipment(Request $request)
    {
        ShipmentCommission::updateOrCreate(
            ['shipment_company_id' => null],
            $request->validate([
                'annual_subscription' => 'required|numeric',
                'shipment_commission_percent' => 'required|numeric',
                'shipment_commission_min' => 'required|numeric',
                'annual_target' => 'required|numeric',
            ])
        );

        return back()->with('success', 'Shipment commission saved successfully');
    }

    public function storeAdministrative(Request $request)
    {
        VendorCommission::updateOrCreate(
            ['vendor_id' => null],
            $request->validate([
                'refund_fee_percent' => 'required|numeric',
                'refund_fee_min' => 'required|numeric',
            ])
        );

        return back()->with('success', __('admin-dashboard.administrative-fees-saved'));
    }


    public function CustomVendorStore(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'annual_subscription' => 'required|numeric',
            'order_commission_percent' => 'required|numeric',
            'order_commission_min' => 'required|numeric',
            'annual_target_commission' => 'required|numeric',
            'refund_fee_percent' => 'required|numeric',
            'refund_fee_min' => 'required|numeric',
        ]);

        $data['vendor_id'] = $vendor->id;

        VendorCommission::create($data);

        return back()->with('success', __('admin-dashboard.vendor_commission_created'));
    }

    public function CustomVendorUpdate(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'annual_subscription' => 'required|numeric',
            'order_commission_percent' => 'required|numeric',
            'order_commission_min' => 'required|numeric',
            'annual_target_commission' => 'required|numeric',
            'refund_fee_percent' => 'required|numeric',
            'refund_fee_min' => 'required|numeric',
        ]);

        $vendor->commission->update($data);

        return back()->with('success', __('admin-dashboard.vendor_commission_updated'));
    }

    public function CustomShipmentStore(Request $request, ShipmentCompany $shipmentCompany)
    {
        $data = $request->validate([
            'annual_subscription'          => 'required|numeric',
            'shipment_commission_percent'  => 'required|numeric',
            'shipment_commission_min'      => 'required|numeric',
            'annual_target'                => 'required|numeric',
        ]);

        $data['shipment_company_id'] = $shipmentCompany->id;

        ShipmentCommission::create($data);

        return back()->with(
            'success',
            __('admin-dashboard.shipment_commission_created')
        );
    }

    public function CustomShipmentUpdate(Request $request, ShipmentCompany $shipmentCompany)
    {
        $data = $request->validate([
            'annual_subscription'          => 'required|numeric',
            'shipment_commission_percent'  => 'required|numeric',
            'shipment_commission_min'      => 'required|numeric',
            'annual_target'                => 'required|numeric',
        ]);

        $shipmentCompany->commission->update($data);

        return back()->with(
            'success',
            __('admin-dashboard.shipment_commission_updated')
        );
    }


}
