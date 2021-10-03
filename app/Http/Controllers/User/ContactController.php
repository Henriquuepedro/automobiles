<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\User\ContactForm;
use App\Models\Store;
use App\Models\ContactFormClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    private $store;
    private $contactFormClient;

    public function __construct(Store $store, ContactFormClient $contactFormClient)
    {
        $this->contactFormClient = $contactFormClient;
        $this->store = $store;
    }

    public function index()
    {
        return view('user.contact.index');
    }

    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $store = $this->store->getStoreByStore($this->getStoreDomain(), true);

            config()->set('mail.username',      $store->mail_contact_email);
            config()->set('mail.password',      $store->mail_contact_password);
            config()->set('mail.host',          $store->mail_contact_smtp);
            config()->set('mail.port',          $store->mail_contact_port);
            config()->set('mail.from.address',  $store->mail_contact_email);
            config()->set('mail.encryption',    $store->mail_contact_security);
            config()->set('mail.from.name',     $store->store_fancy);

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            if ($this->contactFormClient->getMessageLastHour($ip, $store->id, 2))
                return response()->json(array('success' => false, 'message' => 'Você já enviou muitas mensagens. Tente enviar novamente mais tarde!'));

            $idForm = $this->contactFormClient->insert(
                array(
                    "name"      => filter_var($request->name    ?? '', FILTER_SANITIZE_STRING),
                    "email"     => filter_var($request->email   ?? '', FILTER_SANITIZE_EMAIL),
                    "subject"   => filter_var($request->subject ?? '', FILTER_SANITIZE_STRING),
                    "phone"     => filter_var($request->phone   ?? '', FILTER_SANITIZE_STRING),
                    "message"   => filter_var($request->message ?? '', FILTER_SANITIZE_STRING),
                    "company_id"=> $store->company_id,
                    "store_id"  => $store->id,
                    "ip"        => $ip
                )
            );

            $request->request->add(['mail_to' => $store->contact_email]);

            Mail::send(new ContactForm($request));

            $this->contactFormClient->updateSended($idForm->id);

            return response()->json(array('success' => true, 'message' => 'Mensagem enviada com sucesso!'));
        } catch (\Exception $e) {
            if (env('APP_ENV') === 'production') {
                return response()->json(array('success' => true, 'message' => 'Mensagem enviada com sucesso!'));
            }

            return response()->json(array('success' => false, 'message' => "Não foi possível realizar o envio!\n\n {$e->getMessage()} \n\n".json_encode(DB::getQueryLog())));
        }
    }
}
