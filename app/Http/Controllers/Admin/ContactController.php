<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\ContactFormClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private Store $store;
    private ContactFormClient $contactFormClient;

    public function __construct(Store $store, ContactFormClient $contactFormClient)
    {
        $this->contactFormClient = $contactFormClient;
        $this->store = $store;
    }
    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.contactForm.index', compact('stores'));
    }

    public function fetchContactData(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();

        $filters        = [];
        $ini            = $request->input('start');
        $draw           = $request->input('draw');
        $length         = $request->input('length');
        // Filtro do front
        $store_id   = null;

        // valida se usuario pode ver a loja
        if (!empty($request->input('store_id')) && !in_array($request->input('store_id'), $this->getStoresByUsers())) {
            return response()->json(array());
        }

        if (!empty($request->input('store_id')) && !is_array($request->input('store_id'))) {
            $store_id = array($request->input('store_id'));
        }

        if ($request->input('store_id') === null) {
            $store_id = $this->getStoresByUsers();
        }

        $filters['store_id'] = $store_id;
        $filters['value'] = null;

        $search = $request->input('search');
        if ($search['value']) {
            $filters['value'] = $search['value'];
        }

        if (isset($request->order)) {
            if ($request->input('order')[0]['dir'] == "asc") {
                $direction = "asc";
            }
            else {
                $direction = "desc";
            }

            $fieldsOrder = array('id','name','rate','active','primary','created_at', '');
            if (count($this->getStoresByUsers()) > 1) {
                $fieldsOrder[6] = 'store_id';
                $fieldsOrder[7] = '';
            }
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->contactFormClient->getContacts($filters, $ini, $length, $orderBy);


        foreach ($data as $key => $value) {
            $button = "<a href='".route('admin.contactForm.view', ['id' => $value['id']])."' class='btn btn-primary btn-flat btn-sm' data-toggle='tooltip' title='Visualizar'><i class='fa fa-eye'></i></a>
                 <button class='btn btn-danger btn-flat btn-sm btnRequestDeleteContact' contact-id='{$value['id']}' data-toggle='tooltip' title='Excluir'><i class='fa fa-trash'></i></button>";
            $array = array(
                $value['subject'],
                $value['name'],
                $value['email'],
                date('d/m/Y H:i', strtotime($value['created_at']))
            );

            if (count($this->getStoresByUsers()) > 1) {
                array_push($array, $value['store_fancy']);
            }

            array_push($array, $button);

            $result[$key] = $array;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->contactFormClient->getCountContacts($filters, false),
            "recordsFiltered" => $this->contactFormClient->getCountContacts($filters),
            "data" => $result
        );

        return response()->json($output);
    }

    public function remove($contact_id): JsonResponse
    {
        $contact = $this->contactFormClient->getContact($contact_id);

        if (!$contact) {
            return response()->json(array(
                'success' => false,
                'message' => 'Contato não encontrado!' . $contact_id
            ));
        }

        // Loja informada ou usuário não tem permissão
        if (!in_array($contact->store_id, $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja para cadastro!'
            ));
        }

        $delete = $this->contactFormClient->remove(($contact_id));

        if ($delete) {
            return response()->json(array(
                'success' => true,
                'message' => 'Contato excluído com sucesso!'
            ));
        }

        return response()->json(array(
            'success' => false,
            'message' => 'Não foi possível excluir o contato, tente novamente'
        ));
    }

    public function view(int $id)
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        $dataContact = $this->contactFormClient->getContact($id);

        // Loja informada ou usuário não tem permissão
        if (!$dataContact || !in_array($dataContact->store_id, $this->getStoresByUsers())) {
            return redirect()->route('admin.contactForm.index');
        }

        return view('admin.contactForm.view', compact('dataContact', 'stores'));
    }
}
