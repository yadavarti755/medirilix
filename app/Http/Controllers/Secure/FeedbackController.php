<?php

namespace App\Http\Controllers\Secure;

use Str;
use App\DTO\FeedbackDto;
use App\Exports\FeedbacksExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Services\FeedbackService;
use Exception;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackController extends Controller
{
    protected $feedbackService;
    public function __construct()
    {
        $this->feedbackService = new FeedbackService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = __('title.feedback');
        return view('secure.feedbacks.index', compact('pageTitle'));
    }

    /**
     * Fetch a listing of the resource.
     */
    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {

            $query  = $this->feedbackService->findAll();

            // Apply date filters
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($feedback) {
                    $button = '';
                    if (auth()->user()->can('view feedback')) {
                        $button .= '<a href="' . route('feedbacks.show', $feedback->id) . '" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a> ';
                    }

                    if (auth()->user()->can('delete feedback')) {
                        $button .= '<button class="btn btn-sm btn-danger delete-feedback" data-id="' . $feedback->id . '" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';
                    }
                    return $button;
                })
                ->addColumn('message_brief', function ($feedback) {
                    return Str::words($feedback->message, 20);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $fileName = 'feedbacks_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new FeedbacksExport($startDate, $endDate), $fileName);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = __('title.view_feedback');
        $feedback = $this->feedbackService->findById($id);
        return view('secure.feedbacks.show', compact('feedback', 'pageTitle'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $feedback = $this->feedbackService->delete($id);
            if (!$feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting feedback.',
                ], 500);
            }

            return response()->json(['message' => 'Feedback moved to trash successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Feedback deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }
    }

    public function showFeedbackForm()
    {
        $pageTitle = __('title.feedback');
        $parentPageTitle = __('title.feedback_parent');

        return view('website.feedback', compact('pageTitle', 'parentPageTitle'));
    }

    public function saveFeedback(StoreFeedbackRequest $request)
    {

        try {
            $dto = new FeedbackDto(
                $request->name,
                $request->email,
                $request->mobile_no ?? null,
                $request->message
            );

            $result = $this->feedbackService->create($dto);

            if (!$result) {
                return response()->json([
                    'message' => __('title.feedback_error')
                ], 500);
            }

            return response()->json([
                'message' => __('title.feedback_saved')
            ]);
        } catch (Exception $e) {
            Log::error('Feedback submission failed: ' . $e->getMessage());
            return response()->json([
                'message' => __('title.feedback_exception')
            ], 500);
        }
    }
}
