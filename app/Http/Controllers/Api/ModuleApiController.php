<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZoonoticDisease;
use App\Models\WeightRecord;
use App\Models\Hospitalization;
use App\Models\TreatmentPlan;
use App\Models\LaboratoryOrder;
use App\Models\ImagingExam;
use App\Models\Referral;
use App\Models\ConsentForm;
use App\Models\Pet;
use Illuminate\Http\Request;

class ModuleApiController extends Controller
{
    public function zoonoticDiseases(Request $request)
    {
        $query = ZoonoticDisease::active();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('causative_agent', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function weightRecords(Request $request)
    {
        $query = WeightRecord::with(['pet', 'measuredBy']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        return response()->json($query->orderBy('measurement_date', 'desc')->paginate(20));
    }

    public function hospitalizations(Request $request)
    {
        $query = Hospitalization::with(['pet', 'tutor', 'vet', 'dailyRecords', 'fluidTherapies', 'prescriptions']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('admission_date', 'desc')->paginate(20));
    }

    public function treatmentPlans(Request $request)
    {
        $query = TreatmentPlan::with(['pet', 'tutor', 'vet', 'items']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function laboratoryOrders(Request $request)
    {
        $query = LaboratoryOrder::with(['pet', 'vet', 'tests']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('order_date', 'desc')->paginate(20));
    }

    public function imagingExams(Request $request)
    {
        $query = ImagingExam::with(['pet', 'vet', 'radiologist']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('exam_date', 'desc')->paginate(20));
    }

    public function referrals(Request $request)
    {
        $query = Referral::with(['pet', 'referringVet', 'receivingVet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function consentForms(Request $request)
    {
        $query = ConsentForm::with(['pet', 'tutor', 'template', 'veterinarian', 'witness']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }
}
