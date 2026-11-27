<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Http\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}
 
    public function index(Request $request)
    {
        $query = Invoice::with('items')->latest();

//show invoice by customer name with pagination
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('customer_name', 'like', "%{$searchTerm}%");
        }

//show invoices with pagination
        $invoices = $query->paginate($request->integer('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
    //insert to DB with all layers of security
        $invoice = $this->invoiceService->createInvoice($request->validated());

        return (new InvoiceResource($invoice))
            ->response();
    }
//get invoice by number or invoice_number
    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load('items'));
    }
}
?>