<?php

namespace App\Http\Controllers;

use App\Services\PartnerEnquiryService;
use Illuminate\Http\Request;

class PartnerEnquiryController extends Controller
{
    protected $enquiryService;

    /**
     * Constructor with dependency injection
     *
     * @param PartnerEnquiryService $enquiryService
     */
    public function __construct(PartnerEnquiryService $enquiryService)
    {
        $this->enquiryService = $enquiryService;
    }

    /**
     * Store a new partner enquiry (public endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Check rate limiting
        if ($this->isRateLimited($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many submission attempts. Please try again in a few minutes.'
            ], 429);
        }

        // Use service to handle the enquiry
        $result = $this->enquiryService->submitEnquiry(
            $request->all(),
            $request->ip(),
            $request->userAgent()
        );

        // Return appropriate response
        $statusCode = $result['success'] ? 201 : 422;
        
        return response()->json($result, $statusCode);
    }

    /**
     * Get all enquiries with filters and pagination (protected)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'date_from', 'date_to']);
        $perPage = $request->get('per_page', 20);
        
        $enquiries = $this->enquiryService->getEnquiries($filters, $perPage);
        
        return response()->json([
            'success' => true,
            'data' => $enquiries
        ]);
    }

    /**
     * Delete an enquiry (protected)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->enquiryService->deleteEnquiry($id);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Enquiry deleted successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Enquiry not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting enquiry'
            ], 500);
        }
    }

    /**
     * Get enquiry statistics (protected)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $stats = $this->enquiryService->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Simple rate limiting check
     *
     * @param Request $request
     * @return bool
     */
    private function isRateLimited(Request $request): bool
    {
        $key = 'partner_enquiry:' . $request->ip();
        $maxAttempts = 5; // 5 attempts
        $decayMinutes = 15; // per 15 minutes
        
        if (cache()->has($key . ':blocked')) {
            return true;
        }
        
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            cache()->put($key . ':blocked', true, $decayMinutes * 60);
            return true;
        }
        
        cache()->put($key, $attempts + 1, $decayMinutes * 60);
        return false;
    }
}