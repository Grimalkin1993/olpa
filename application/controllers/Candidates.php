<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    class Candidates extends CI_Controller {
        public function __construct()
        {
            parent::__construct();
        }

        // SSG Area
        public function index(){
            $data['menu'] = 'election';
            $data['submenu'] = 'candidate';

            $this->load->view('templates/header', $data);
            $this->load->view('admin/candidate');
        }

        public function candidates_list()
        {
            $list = $this->candidate_model->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $list) {
                $no++;
                $row = array();
                $row[] = $list->id;
                $row[] = $list->student_id_number . ' - ' . $list->lastname . ', ' . $list->firstname;
                $row[] = $list->position;
                $row[] = $list->partylist;
                $row[] = $list->time;
                $row[] = $list->date;
                //add html for action
                $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit('."'".$list->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>';

                $data[] = $row;
            }

            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->candidate_model->count_all(),
                "recordsFiltered" => $this->candidate_model->count_filtered(),
                "data" => $data,
            );
            //output to json format
            echo json_encode($output);
        }

        public function create() {

            $this->_validate();

            $data = Array (
                'student_id' => $this->input->post('student_id'),
                'position_id' => $this->input->post('position_id'),
                'partylist_id' => $this->input->post('partylist_id'),
                'status' => 'inactive'
            );
            $this->candidate_model->create($data);

            echo json_encode(array("status" => TRUE));
        }

        public function edit($id)
        {
            $data = $this->candidate_model->get_by_id($id);
            echo json_encode($data);
        }

        public function update() {
            $this->_validate();

            if($this->input->post('onChange_student') != '') {
                $data = Array (
                    'student_id' => $this->input->post('student_id'),
                    'position_id' => $this->input->post('position_id'),
                    'partylist_id' => $this->input->post('partylist_id'),
                );
            } else {
                $data = Array (
                    'position_id' => $this->input->post('position_id'),
                    'partylist_id' => $this->input->post('partylist_id'),
                );
            }
            $this->candidate_model->update(array('id' => $this->input->post('id')), $data);

            echo json_encode(array("status" => TRUE));
        }

        /////////////////////////////////// end of SSG

        private function _validate()
        {
            $data = array();
            $data['error_string'] = array();
            $data['inputerror'] = array();
            $data['status'] = TRUE;

            $student_id = $this->input->post('student_id');
            $position_id = $this->input->post('position_id');
            $partylist_id = $this->input->post('partylist_id');
            $onChange_student = $this->input->post('onChange_student');

            if (isset($onChange_student) && $onChange_student != ''){
                if (isset($student_id) && $this->check_student_id_exists($student_id) === FALSE) {
                    $data['inputerror'][] = 'student_id';
                    $data['error_string'][] = 'Sorry! That Student is already on the list. Please choose a different one';
                    $data['status'] = FALSE;
                }
            }

            if(isset($student_id) && $student_id == '')
            {
                $data['inputerror'][] = 'student_id';
                $data['error_string'][] = 'Please Select a Student';
                $data['status'] = FALSE;
            }

            if(isset($position_id) && $position_id == '')
            {
                $data['inputerror'][] = 'position_id';
                $data['error_string'][] = 'Please Select a Position';
                $data['status'] = FALSE;
            }

            if(isset($partylist_id) && $partylist_id == '')
            {
                $data['inputerror'][] = 'partylist_id';
                $data['error_string'][] = 'Please Select a Party';
                $data['status'] = FALSE;
            }


            if($data['status'] === FALSE)
            {
                echo json_encode($data);
                exit();
            }
        }

        //Check if exists
        public function check_student_id_exists($student_id){
            if($this->candidate_model->check_student_id_exists($student_id)){
                return true;
            } else {
                return false;
            }
        }
    }