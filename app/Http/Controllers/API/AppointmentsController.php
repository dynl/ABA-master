<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class AppointmentsController extends Controller
{
    // ... existing index function ...
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                $appointments = Appointment::where('email', $user->email)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $appointments = [];
            }

            return response()->json([
                'success' => true,
                'message' => 'User appointments retrieved successfully',
                'data' => $appointments
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- UPDATED STORE FUNCTION ---
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'age' => 'required',
                'sex' => 'required|string',
                'animal_type' => 'required|string',
                'date' => 'required',
                'time' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 1. CHECK FOR DOUBLE BOOKING
            $exists = Appointment::where('date', $request->date)
                ->where('time', $request->time)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This date and time slot is already booked. Please choose another.'
                ], 409); // 409 = Conflict
            }

            // 2. Create Appointment
            $data = $validator->validated();
            $data['phone_number'] = 'N/A';
            $data['email'] = $request->user() ? $request->user()->email : 'N/A';
            $data['patient_id'] = null;

            $appointment = Appointment::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully',
                'data' => $appointment
            ], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ... existing show and getAvailability functions ...
    public function show($id)
    {
        try {
            $appointment = Appointment::find($id);
            if (!$appointment) return response()->json(['success' => false, 'message' => 'Not found'], 404);
            return response()->json(['success' => true, 'data' => $appointment], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getAvailability()
    {
        try {
            $counts = DB::table('appointments')
                ->select('date', DB::raw('count(*) as total'))
                ->groupBy('date')
                ->get();
            return response()->json(['success' => true, 'data' => $counts], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
            }

            $appointment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
