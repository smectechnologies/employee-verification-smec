<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $request = service('request');
        $employee = null;
        $error = null;
        $empId = trim($request->getGet('employee_id'));
        $dob = trim($request->getGet('dob'));
        if ($empId && $dob) {
            $employeeModel = new \App\Models\EmployeeModel();
            $employee = $employeeModel->where('employee_id', $empId)
                ->where('dob', $dob)
                ->where('approved', 1)
                ->first();
            log_message('debug', 'Employee search: employee_id=' . $empId . ', dob=' . $dob . ', result=' . print_r($employee, true));
            if (!$employee) {
                $error = 'No approved employee found with the given ID and Date of Birth.';
            }
        }
        return view('employee_verification', [
            'employee' => $employee,
            'error' => $error
        ]);
    }

    public function publicDownload($docId)
    {
        $documentModel = new \App\Models\DocumentModel();
        $doc = $documentModel->find($docId);
        if (!$doc) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }
        // Check if the employee is approved
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->find($doc['employee_id']);
        if (!$employee || !$employee['approved']) {
            return $this->response->setStatusCode(403)->setBody('Unauthorized');
        }
        $folder = '';
        if ($doc['doc_type'] === 'offer_letter') {
            $folder = WRITEPATH . 'uploads/offer_letters/';
        } elseif ($doc['doc_type'] === 'experience_certificate') {
            $folder = WRITEPATH . 'uploads/experience_certificates/';
        } else {
            $folder = WRITEPATH . 'uploads/other_certificates/';
        }
        $filePath = $folder . $doc['file_name'];
        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }
        $ext = strtolower(pathinfo($doc['file_name'], PATHINFO_EXTENSION));
        $asAttachment = !in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        return $this->response->download($filePath, null, $asAttachment)->setFileName($doc['original_name']);
    }
}
