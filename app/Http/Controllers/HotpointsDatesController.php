<?php

namespace App\Http\Controllers;

use App\Models\HotpointsDate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class HotpointsDatesController extends Controller
{
    /**
     * Actualizar los hotpoints desde AJAX
     */
    public function updateHotpoints(Request $request): JsonResponse
    {
        try {
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|integer',
                'id' => 'required|integer',
                'updates' => 'required|array',
                'updates.*.product_id' => 'required|integer',
                'updates.*.status' => 'required|boolean',
                'updates.*.price' => 'required|numeric|min:0',
                'updates.*.date_in' => 'nullable|string',
                'updates.*.date_out' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $projectId = $request->input('project_id');
            $id = $request->input('id');
            $updates = $request->input('updates');
            $results = [];
            $errors = [];

            foreach ($updates as $index => $updateData) {
                try {
                    // Preparar datos para la actualización
                    $data = [
                        'estado' => $updateData['status'],
                        'price' => (float) $updateData['price'],
                    ];

                    // Procesar date_in - convertir de dd/mm/yyyy a Y-m-d
                    if (!empty($updateData['date_in']) && $updateData['date_in'] !== '---') {
                        $data['date_in'] = $this->convertDateFormat($updateData['date_in']);
                    } else {
                        $data['date_in'] = null;
                    }

                    // Procesar date_out - convertir de dd/mm/yyyy a Y-m-d
                    if (!empty($updateData['date_out']) && $updateData['date_out'] !== '---') {
                        $data['date_out'] = $this->convertDateFormat($updateData['date_out']);
                    } else {
                        $data['date_out'] = null;
                    }

                    // Actualizar o crear el registro
                    $hotpoint = HotpointsDate::updateOrCreateByKeys(
                        $projectId,
                        $updateData['product_id'],
						$id,
                        $data
                    );

                    $results[] = [
                        'product_id' => $updateData['product_id'],
                        'success' => true,
                        'action' => $hotpoint->wasRecentlyCreated ? 'created' : 'updated'
                    ];

                    // Log para debugging
                    Log::info('Hotpoint updated', [
                        'project_id' => $projectId,
                        'product_id' => $updateData['product_id'],
						'id' => $id,
                        'action' => $hotpoint->wasRecentlyCreated ? 'created' : 'updated',
                        'original_dates' => [
                            'date_in' => $updateData['date_in'] ?? null,
                            'date_out' => $updateData['date_out'] ?? null
                        ],
                        'converted_data' => $data
                    ]);

                } catch (\Exception $e) {
                    $errors[] = [
                        'product_id' => $updateData['product_id'],
                        'error' => $e->getMessage()
                    ];

                    Log::error('Error updating hotpoint', [
                        'project_id' => $projectId,
                        'product_id' => $updateData['product_id'],
						'id' => $id,
                        'error' => $e->getMessage(),
                        'original_data' => $updateData
                    ]);
                }
            }

            // Preparar respuesta
            $response = [
                'success' => true,
                'message' => 'Actualización completada',
                'results' => $results,
                'summary' => [
                    'total' => count($updates),
                    'successful' => count($results),
                    'errors' => count($errors)
                ]
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
                $response['message'] .= ' (con algunos errores)';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error general en updateHotpoints', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convertir fecha de formato dd/mm/yyyy a Y-m-d
     */
    private function convertDateFormat($dateString)
    {
        if (empty($dateString) || $dateString === '---') {
            return null;
        }

        // Si ya está en formato Y-m-d, devolverla tal como está
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        // Si está en formato dd/mm/yyyy, convertir a Y-m-d
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            
            // Validar que sea una fecha válida
            if (checkdate($month, $day, $year)) {
                return $year . '-' . $month . '-' . $day;
            } else {
                throw new \InvalidArgumentException("Fecha inválida: {$dateString}");
            }
        }

        // Si está en algún otro formato, intentar parsearlo con Carbon
        try {
            $carbonDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
            return $carbonDate->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Formato de fecha no reconocido: {$dateString}");
        }
    }

    /**
     * Obtener hotpoints por proyecto (opcional, para debugging)
     */
    public function getHotpointsByProject(Request $request, $projectId): JsonResponse
    {
        try {
            $hotpoints = HotpointsDate::where('project_id', $projectId)->get();
            
            return response()->json([
                'success' => true,
                'data' => $hotpoints->map(function ($hotpoint) {
                    return [
                        'project_id' => $hotpoint->project_id,
                        'product_id' => $hotpoint->product_id,
                        'estado' => $hotpoint->estado,
                        'estado_text' => $hotpoint->estado_text,
                        'price' => $hotpoint->price,
                        'date_in' => $hotpoint->get_date_in(),
                        'date_out' => $hotpoint->get_date_out(),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los hotpoints',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}