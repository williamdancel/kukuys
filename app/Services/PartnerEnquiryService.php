<?php

namespace App\Services;

use App\Models\PartnerEnquiry;
use Illuminate\Support\Facades\Validator;

class PartnerEnquiryService
{
    /**
     * Submit a new partner enquiry
     *
     * @param array $data
     * @param string $ip
     * @param string $userAgent
     * @return array
     */
    public function submitEnquiry(array $data, string $ip, string $userAgent): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'company' => 'required|string|max:50',
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ];
        }

        try {
            $enquiry = PartnerEnquiry::create($validator->validated());

            return [
                'success' => true,
                'message' => 'Your enquiry has been submitted successfully. We will get back to you soon.',
                'data' => $enquiry
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while submitting your enquiry. Please try again later.'
            ];
        }
    }

    /**
     * Get all enquiries with filters and pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getEnquiries(array $filters, int $perPage = 20)
    {
        $query = PartnerEnquiry::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Date from filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        // Date to filter
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Delete an enquiry
     *
     * @param int $id
     * @return bool
     */
    public function deleteEnquiry(int $id): bool
    {
        $enquiry = PartnerEnquiry::find($id);
        
        if (!$enquiry) {
            return false;
        }

        return $enquiry->delete();
    }

    /**
     * Get enquiry statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => PartnerEnquiry::count(),
            'today' => PartnerEnquiry::whereDate('created_at', today())->count(),
            'this_week' => PartnerEnquiry::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => PartnerEnquiry::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
}