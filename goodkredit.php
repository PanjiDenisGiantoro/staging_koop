<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Application_api extends MY_Controller
{
    public function __construct()
    {
        // handling CORS
        header('Access-Control-Allow-Origin: http://localhost/ikoop/demo/index.php');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method, pragma, cache-control, expires");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();

        $this->load->helper('general');
        $this->load->database();
    }

    public function add_process()
    {

        $this->load->library('far_borrower');

        // Validate app request method and credential
        $post_data = validate_api_request('POST');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $current_date = date('Y-m-d H:i:s');

        $data_borrower_detail = array(
            'app_userid' => $post_data['NRIC'],
            'fullname' => $post_data['FullName'],
            'nric' => $post_data['NRIC'],
            'address_1' => $post_data['Address1'],
            'address_2' => $post_data['Address2'],
            'ls_id' => $post_data['State'],
            'postcode' => $post_data['Postcode'],
            'email' => $post_data['Email'],
            'mobile' => $post_data['Mobile'],
            'create_dttm' => $current_date,
            // 'app_borrowerid' => $post_data['BorrowerId'], // if nothing goes wrong delete this
        );

        // insert data in advance_loan_detail
        $data_advance_loan = array(
            'app_userid' => $post_data['NRIC'],
            'loan_amount' => $post_data['LoanAmount'],
            'repayment_period' => $post_data['RepaymentPeriod'],
            'employment_type' => $post_data['EmploymentType'],
            'company_name' => $post_data['CompanyName'],
            'company_address_1' => $post_data['CompanyAddress1'],
            'company_address_2' => $post_data['CompanyAddress2'],
            'company_contact' => $post_data['CompanyContact'],
            'company_position' => $post_data['CompanyPosition'],
            'occupation_segment' => $post_data['OccupationSegment'],
            'income_type' => $post_data['IncomeType'],
            'industry' => $post_data['Industry'],
            'monthly_income' => $post_data['MonthlyIncome'],
            'monthly_commitment' => $post_data['MonthlyCommitment'],
            'poa_id' => $post_data['POAType'],
            'purpose_of_application' => $post_data['POADescription'],
            'emergency_fullname' => $post_data['EmergencyFullName'],
            'emergency_relationship' => $post_data['EmergencyRelationship'],
            'emergency_mobile' => $post_data['EmergencyMobile'],
            'create_dttm' => $current_date,
            'form_open_dttm' => $post_data['FormOpenDttm'],
            'form_finish_dttm' => $post_data['FormFinishDttm'],
            // 'time_taken_to_finish_form' => '',
            // 'time_taken_from_created_to_finished' => '',
            'geocode_latitude' => $post_data['Latitude'],
            'geocode_longitude' => $post_data['Longitude'],
            'ip_address' => $post_data['IpAddress'],
            'nric_front_url' => $post_data['NRICFront'],
            'nric_back_url' => $post_data['NRICBack'],
            'payslip_1_url' => $post_data['Payslip1'],
            'payslip_2_url' => $post_data['Payslip2'],
            'payslip_3_url' => $post_data['Payslip3'],
            'bankstatement_1_url' => $post_data['BankStatement1'],
            'bankstatement_2_url' => $post_data['BankStatement2'],
            'bankstatement_3_url' => $post_data['BankStatement3'],
            'bform_url' => $post_data['BForm'],
            'utilitiesbill_1_url' => $post_data['UtilitiesBill'],
            'application_status'  => 'user_filled_in_pending_admin_checking'
        );

        $borrower_detail = $this->far_borrower->get_borrower_detail('app_userid', $post_data['NRIC']);
        if(count($borrower_detail) == 0){ // not yet exist in our database
            // insert data in borrower_detail            
            $this->db->insert('borrower_detail', $data_borrower_detail);
            $insertIdBorrowerDetail = $this->db->insert_id();
        
            // random alphanumeric generator
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $data_advance_loan['page_alias'] = $insertIdBorrowerDetail.strtoupper(substr(str_shuffle($permitted_chars), 0, 10));
            
            // insert data in advance loan detail
            $this->db->insert('advance_loan_detail', $data_advance_loan);
            $insertIdAdvanceLoan = $this->db->insert_id();
            $this->form_scoring($insertIdAdvanceLoan, $post_data['FormOpenDttm'], $post_data['FormFinishDttm']);
        }else{ // exist in database. we do update
            // start with borrower_detail
            $this->db->where('app_userid', $post_data['NRIC']);
            $this->db->update('borrower_detail', $data_borrower_detail);

            // then advance loan form
            $this->db->where('app_userid', $post_data['NRIC']);
            $this->db->update('advance_loan_detail', $data_advance_loan);
            $insertIdAdvanceLoan = $this->db->insert_id();
            $this->form_scoring($insertIdAdvanceLoan, $post_data['FormOpenDttm'], $post_data['FormFinishDttm']);
        }

        $sys_status = '1';
        $sys_message = 'Add application success';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
        );

        // Response data in json format
        api_response($result);
    }

    private function form_scoring($application_id, $form_open_dttm, $form_finish_dttm){
        $this->load->library('far_score');
        $this->load->library('far_advance_loan');
        $this->load->library('far_meta');
        // $form_finish_dttm = date("Y-m-d H:i:s");
        // $this->far_advance_loan->update_advance_loan_detail('application_id', $application_id, 'application_status', 'user_filled_in_pending_admin_checking');
        $this->far_advance_loan->update_advance_loan_detail('application_id', $application_id, 'form_finish_dttm', $form_finish_dttm);
        
        //calculate time taken to fill-in the form
        $timeFirst  = strtotime($form_open_dttm);
        $timeSecond = strtotime($form_finish_dttm);
        $differenceInSeconds = $timeSecond - $timeFirst;
        //time_taken_to_finish_form
        $this->far_advance_loan->update_advance_loan_detail('application_id', $application_id, 'time_taken_to_finish_form', $differenceInSeconds);
        if($differenceInSeconds < 60){
            $typing_score_amount = $this->far_meta->get_value('score_form_finish_less_than_1_minute');
        }else{
            $typing_score_amount = $this->far_meta->get_value('score_form_finish_more_than_1_minute');
        }
        $score_data = array(
            'application_id' => $application_id,
            'ref' => 'typing_speed',
            'sender' => '-1',
            'receiver' => $application_id,
            'amount' => $typing_score_amount, //-2
            'status' => 'success',
            'remarks' => "Typing time less than 1 minute",
            'create_dttm' => date("Y-m-d H:i:s"),
                
        );
        $this->far_score->insert_score($score_data);
        
        //calculate time to finish form
        $this->far_score->calculate_time_to_finish_form($application_id);
        
        //send email saying success
        // $this->far_email->send_email_finish_fill_in_advance_form($application_id);
    }

    public function add_mobile_contacts(){
        // Validate app request method and credential
        $post_data = validate_api_request('POST');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $current_date = date('Y-m-d H:i:s');


        // check method. if add clear all record in db
        if ($post_data['Method'] == 'add') {
            $this->db->where('uniqueid', $post_data['UniqueId']);
            $this->db->where('mobile', $post_data['Mobile']);
            $this->db->delete('mobile_contacts');
        }

        // insert data in borrower_detail
        $data = array(
            'uniqueid'    => $post_data['UniqueId'],
            'mobile'        => $post_data['Mobile'],
            'list'          => json_encode($post_data['ContactLists']),
            'create_dttm'   => $current_date,
        );
        $this->db->insert('mobile_contacts', $data);

        // logs
        // log_message('error', 'NEW MOBILE CONTACTS API CALL');
        // log_message('error', json_encode($post_data));

        $sys_status = '1';
        $sys_message = 'Add mobile contact success';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
        );

        // Response data in json format
        api_response($result);
    }

    public function add_mobile_calendars(){
        // Validate app request method and credential
        $post_data = validate_api_request('POST');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $current_date = date('Y-m-d H:i:s');

        // check method. if add clear all record in db
        if ($post_data['Method'] == 'add') {
            $this->db->where('uniqueid', $post_data['UniqueId']);
            $this->db->where('mobile', $post_data['Mobile']);
            $this->db->delete('mobile_calendars');
        }

        // insert data in borrower_detail
        $data = array(
            'uniqueid'    => $post_data['UniqueId'],
            'mobile'        => $post_data['Mobile'],
            'list'          => json_encode($post_data['CalendarLists']),
            'create_dttm'   => $current_date,
        );
        $this->db->insert('mobile_calendars', $data);


        $sys_status = '1';
        $sys_message = 'Add mobile calendar success';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
        );

        // Response data in json format
        api_response($result);
    }

    public function add_process_legacy()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('POST');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $current_date = date('Y-m-d H:i:s');

        // insert data in borrower_detail
        $data = array(
            'app_userid' => $post_data['NRIC'],
            'fullname' => $post_data['FullName'],
            'nric' => $post_data['NRIC'],
            'address_1' => $post_data['Address1'],
            'address_2' => $post_data['Address2'],
            'ls_id' => $post_data['State'],
            'postcode' => $post_data['Postcode'],
            'email' => $post_data['Email'],
            'mobile' => $post_data['Mobile'],
            'create_dttm' => $current_date,
            // 'app_borrowerid' => '',
            // 'age' => '',
            // 'town_name' => '',
            // 'monthly_income' => '',
        );
        $this->db->insert('borrower_detail', $data);

        // insert data in advance_loan_detail
        $nric_front_detail = save_base64_file($post_data['NRICFront'], 'png', 'assets/uploads/nric/');
        $nric_back_detail = save_base64_file($post_data['NRICBack'], 'png', 'assets/uploads/nric/');
        $payslip_1_detail = save_base64_file($post_data['Payslip1'], 'png', 'assets/uploads/payslip/');
        $payslip_2_detail = save_base64_file($post_data['Payslip2'], 'png', 'assets/uploads/payslip/');
        $payslip_3_detail = save_base64_file($post_data['Payslip3'], 'png', 'assets/uploads/payslip/');
        $bankstatement_1_detail = save_base64_file($post_data['BankStatement1'], 'png', 'assets/uploads/bankstatement/');
        $bankstatement_2_detail = save_base64_file($post_data['BankStatement2'], 'png', 'assets/uploads/bankstatement/');
        $bankstatement_3_detail = save_base64_file($post_data['BankStatement3'], 'png', 'assets/uploads/bankstatement/');
        $bform_detail = save_base64_file($post_data['BForm'], 'png', 'assets/uploads/bform/');
        $utilitiesbill_1_detail = save_base64_file($post_data['UtilitiesBill'], 'png', 'assets/uploads/utilitiesbill/');

        $data = array(
            'app_userid' => $post_data['NRIC'],
            'loan_amount' => $post_data['LoanAmount'],
            'repayment_period' => $post_data['RepaymentPeriod'],
            'employment_type' => $post_data['EmploymentType'],
            'company_name' => $post_data['CompanyName'],
            'company_address_1' => $post_data['CompanyAddress1'],
            'company_address_2' => $post_data['CompanyAddress2'],
            'company_contact' => $post_data['CompanyContact'],
            'company_position' => $post_data['CompanyPosition'],
            'occupation_segment' => $post_data['OccupationSegment'],
            'monthly_income' => $post_data['MonthlyIncome'],
            'monthly_commitment' => $post_data['MonthlyCommitment'],
            'poa_id' => $post_data['POAType'],
            'purpose_of_application' => $post_data['POADescription'],
            'emergency_fullname' => $post_data['EmergencyFullName'],
            'emergency_relationship' => $post_data['EmergencyRelationship'],
            'emergency_mobile' => $post_data['EmergencyMobile'],
            'create_dttm' => $current_date,
            'form_open_dttm' => $post_data['FormOpenDttm'],
            'form_finish_dttm' => $post_data['FormFinishDttm'],
            'time_taken_to_finish_form' => '',
            'time_taken_from_created_to_finished' => '',
            // 'page_alias' => '',
            // 'page_link_short_url' => '',
            // 'application_status' => '',
            // 'cs_verified_dttm' => '',
            // 'cs_uacc_id' => '',
            // 'rejected_dttm' => '',
            // 'approved_dttm' => '',
            // 'approved_uacc_id' => '',
            // 'approved_status' => '',
            // 'cron_form_reminder_dttm' => '',
            // 'employment_type' => '',
            // 'occupation_segment' => '',
            // 'income_type' => '',
            // 'industry' => '',
            // 'browser_location_latitude' => '',
            // 'browser_location_longitude' => '',
            // 'browser_location_accuracy' => '',
            // 'facebook_data' => '',
            // 'calculated_dsr' => '',
            // 'dsr_percentage' => '',
            // 'ctos_score' => '',
            // 'ccris_score' => '',
            // 'manual_payslip' => '',
            // 'workplace_verified' => '',
            // 'google_search' => '',
            // 'credit_standing_customer_criteria' => '',
            // 'payslip_coincide_with_bank_statement' => '',
            // 'geocode_latitude' => '',
            // 'geocode_longitude' => '',
            // 'approved_amount' => '',
            // 'confirm_approved_dttm' => '',
            // 'confirm_approved_uacc_id' => '',
            // 'confirm_rejected_dttm' => '',
            // 'confirm_rejected_uacc_id' => '',
            'nric_front_url' => $nric_front_detail['FileURL'],
            'nric_back_url' => $nric_back_detail['FileURL'],
            'payslip_1_url' => $payslip_1_detail['FileURL'],
            'payslip_2_url' => $payslip_2_detail['FileURL'],
            'payslip_3_url' => $payslip_3_detail['FileURL'],
            'bankstatement_1_url' => $bankstatement_1_detail['FileURL'],
            'bankstatement_2_url' => $bankstatement_2_detail['FileURL'],
            'bankstatement_3_url' => $bankstatement_3_detail['FileURL'],
            'bform_url' => $bform_detail['FileURL'],
            'utilitiesbill_1_url' => $utilitiesbill_1_detail['FileURL'],
        );
        $this->db->insert('advance_loan_detail', $data);

        $sys_status = '1';
        $sys_message = 'Add application success';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
        );

        // Response data in json format
        api_response($result);
    }

    public function save_upload_document()
    {
        // Validate app request method and credential
        $post_data = validate_api_request('POST');
        // var_dump($post_data);

        // set default variable
        $sys_status = 0;
        $sys_message = '';

        $data = array();

        (!isset($post_data['NRICFront'])) ?: $data['nric_front_url'] = $post_data['NRICFront'];
        (!isset($post_data['NRICBack'])) ?: $data['nric_back_url'] = $post_data['NRICBack'];
        (!isset($post_data['Payslip1'])) ?: $data['payslip_1_url'] = $post_data['Payslip1'];
        (!isset($post_data['Payslip2'])) ?: $data['payslip_2_url'] = $post_data['Payslip2'];
        (!isset($post_data['Payslip3'])) ?: $data['payslip_3_url'] = $post_data['Payslip3'];
        (!isset($post_data['BankStatement1'])) ?: $data['bankstatement_1_url'] = $post_data['BankStatement1'];
        (!isset($post_data['BankStatement2'])) ?: $data['bankstatement_2_url'] = $post_data['BankStatement2'];
        (!isset($post_data['BankStatement3'])) ?: $data['bankstatement_3_url'] = $post_data['BankStatement3'];
        (!isset($post_data['BForm'])) ?: $data['bform_url'] = $post_data['BForm'];
        (!isset($post_data['UtilitiesBill'])) ?: $data['utilitiesbill_1_url'] = $post_data['UtilitiesBill'];
        
        $this->db->where('application_id', $post_data['application_id']);
        $this->db->update('advance_loan_detail', $data);
        // var_dump($this->db->last_query());

        $sys_status = '1';
        $sys_message = 'update record success';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
        );

        // Response data in json format
        api_response($result);
    }

    public function upload_document()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('POST');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $current_date = date('Y-m-d H:i:s');

        // insert data in advance_loan_detail
        $nric_front_detail = save_base64_file($request_data['NRICFront'], $request_data['NRICFrontFileType'], 'assets/uploads/nric/');
        $nric_back_detail = save_base64_file($request_data['NRICBack'], $request_data['NRICBackFileType'], 'assets/uploads/nric/');
        $payslip_1_detail = save_base64_file($request_data['Payslip1'], $request_data['Payslip1FileType'], 'assets/uploads/payslip/');
        $payslip_2_detail = save_base64_file($request_data['Payslip2'], $request_data['Payslip2FileType'], 'assets/uploads/payslip/');
        $payslip_3_detail = save_base64_file($request_data['Payslip3'], $request_data['Payslip3FileType'], 'assets/uploads/payslip/');
        $bankstatement_1_detail = save_base64_file($request_data['BankStatement1'], $request_data['BankStatement1FileType'], 'assets/uploads/bankstatement/');
        $bankstatement_2_detail = save_base64_file($request_data['BankStatement2'], $request_data['BankStatement2FileType'], 'assets/uploads/bankstatement/');
        $bankstatement_3_detail = save_base64_file($request_data['BankStatement3'], $request_data['BankStatement3FileType'], 'assets/uploads/bankstatement/');
        $bform_detail = save_base64_file($request_data['BForm'], $request_data['BFormFileType'], 'assets/uploads/bform/');
        $utilitiesbill_1_detail = save_base64_file($request_data['UtilitiesBill'], $request_data['UtilitiesBillFileType'], 'assets/uploads/utilitiesbill/');

        $data = array(
            'nric_front_url' => $nric_front_detail['FileURL'],
            'nric_back_url' => $nric_back_detail['FileURL'],
            'payslip_1_url' => $payslip_1_detail['FileURL'],
            'payslip_2_url' => $payslip_2_detail['FileURL'],
            'payslip_3_url' => $payslip_3_detail['FileURL'],
            'bankstatement_1_url' => $bankstatement_1_detail['FileURL'],
            'bankstatement_2_url' => $bankstatement_2_detail['FileURL'],
            'bankstatement_3_url' => $bankstatement_3_detail['FileURL'],
            'bform_url' => $bform_detail['FileURL'],
            'utilitiesbill_1_url' => $utilitiesbill_1_detail['FileURL'],
        );

        $sys_status = '1';
        $sys_message = 'Upload success';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data
        );

        // Response data in json format
        api_response($result);
    }

    public function get_income_type()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('GET');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        $data = [
            ['key' => 'business', 'name' => 'Business'],
            ['key' => 'partnership', 'name' => 'Partnership'],
            ['key' => 'rental', 'name' => 'Rental'],
            ['key' => 'royalties', 'name' => 'Royalties']
        ];

        $sys_status = '1';
        
        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }

    public function get_industry_type()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('GET');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        $data = [
            ['key' => 'retailing_of_goods', 'name' => 'Retailing of Goods'],
            ['key' => 'manufacturing', 'name' => 'Manufacturing'],
            ['key' => 'construction', 'name' => 'Construction'],
            ['key' => 'transporation', 'name' => 'Transporation'],
            ['key' => 'service_oriented', 'name' => 'Service Oriented'],
            ['key' => 'professional_service_firm', 'name' => 'Professional Service Firm']
        ];

        $sys_status = '1';
        
        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }

    public function get_location_state()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('GET');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        $this->db->order_by('ls_name', 'ASC');
        $query = $this->db->get('location_state');
        $data = $query->result_array();

        $sys_status = '1';
        
        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }

    public function get_purpose_of_application()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('GET');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        $this->db->where('poa_status', 'enabled');
        $this->db->order_by('poa_name', 'ASC');
        $query = $this->db->get('purpose_of_application');
        $data = $query->result_array();

        $sys_status = '1';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }

    public function get_occupation_segment()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('GET');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get('segment_list');
        $data = $query->result_array();

        $sys_status = '1';
        
        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }

    /**
     * Get Application Status
     * Return single latest application status and aplication details
     */
    public function get_application_status()
    {
        // Validate app request method and credential
        $request_data = validate_api_request('GET');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        if(!is_null($this->input->get('app_userid')))
        {
            $this->db->where('app_userid', $this->input->get('app_userid'));
            $this->db->order_by('application_id', 'DESC');
            $this->db->limit(1);
            
            $query = $this->db->get('advance_loan_detail');
            $count = $query->num_rows();
            $datas = $query->result_array();
    
            if($count == 1)
            {
                $sys_status = 1;
                switch ($datas[0]['application_status']) {
                    // case 'withdrawn':
                    // case 'paid':
                    // case 'recycle':
                    // case 'confirm_rejected_expired':
                    // case 'expired':
                    // case 'force_rejected':
                    //     $sys_message = "may submit new application";
                    //     break;
                    case 'cancelled':
                    case 'confirm_rejected':
                    case 'confirm_approved_cancelled':
                    case 'cancel':
                        $sys_message = array("application_status_code" => "2", "message" => "retry after date xxxxxx");
                        break;
                    case 'confirm_approved':
                        $sys_message = array("application_status_code" => "3", "message" => "retry after fully paid");
                        break;
                    case 'user_filled_in_pending_admin_checking':
                    case 'approve':
                    case 'rejected':
                        $sys_message = array("application_status_code" => "4", "message" => "processing");
                        break;
                    case 'pending_user_action_to_fill_in':
                        $sys_message = array("application_status_code" => "5", "message" => "continue upload document");
                        break;
                    default:
                        $sys_message = array("application_status_code" => "1", "message" => "may submit new application");
                        break;
                }
                $data = $datas;
            }
            else
            {
                $sys_status = 1;
                $sys_message = array("application_status_code" => "1", "message" => "may submit new application");
            }
        }
        else
        {
            $sys_status = 0;
        }
        

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }

    /**
     * 
     */
    public function set_application_status_paid()
    {
        // Validate app request method and credential
        $post_data = validate_api_request('POST');

        // set default variable
        $sys_status = 0;
        $sys_message = '';
        $data = array();

        // update application_status = paid
        $data = array(
            'application_status' => 'paid'
        );

        $this->db->where('application_id', $post_data['application_id']);
        $this->db->where('app_userid', $post_data['app_userid']);
        $this->db->update('advance_loan_detail', $data);

        $sys_status = '1';
        $sys_message = 'application_status is set to paid';

        $result = array(
            'Status' => $sys_status,
            'SysMessage' => $sys_message,
            'Data' => $data,
        );

        // Response data in json format
        api_response($result);
    }
}
