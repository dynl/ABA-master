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
    // --- GET USER APPOINTMENTS ---
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

    // --- CREATE NEW APPOINTMENT (FIXED) ---
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

            // --- REMOVED DOUBLE BOOKING CHECK ---
            // We commented this out because we want multiple people to be able
            // to book the same day/time (since time is now just "Walk-In")
            /*
            $exists = Appointment::where('date', $request->date)
                ->where('time', $request->time)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This date and time slot is already booked. Please choose another.'
                ], 409); 
            }
            */

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

    // --- SHOW SINGLE APPOINTMENT ---
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

    // --- UPDATE APPOINTMENT (Restored) ---
    public function update(Request $request, $id)
    {
        try {
            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
            }

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

            // Note: We also removed the double-booking check here for edits

            $appointment->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully',
                'data' => $appointment
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- GET AVAILABILITY ---
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

    // --- DELETE APPOINTMENT ---
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
