<?php
namespace App\Controllers;

use App\Models\AdminUserModel;
use CodeIgniter\Controller;

class Admin extends BaseController
{
    public function login()
    {
        if (session()->get('isAdmin')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin_login');
    }

    public function loginProcess()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $adminModel = new AdminUserModel();
        $admin = $adminModel->where('username', $username)->first();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session()->set(['isAdmin' => true, 'admin_id' => $admin['id']]);
            return redirect()->to('/admin/dashboard');
        } else {
            return view('admin_login', ['error' => 'Invalid credentials']);
        }
    }

    public function dashboard()
    {
        if (!session()->get('isAdmin')) {
            return redirect()->to('/admin_login');
        }
        $employeeModel = new \App\Models\EmployeeModel();
        $perPage = 10;
        $page = $this->request->getGet('page') ?? 1;
        $employees = $employeeModel->orderBy('id', 'DESC')->paginate($perPage);
        $pager = $employeeModel->pager;
        return view('dashboard', [
            'employees' => $employees,
            'pager' => $pager
        ]);
    }

    public function addEmployee()
    {
        if (!session()->get('isAdmin')) {
            return redirect()->to('/admin_login');
        }
        $validation = \Config\Services::validation();
        $rules = [
            'employee_id' => 'required|is_unique[employees.employee_id]',
            'name' => 'required',
            'designation' => 'required',
            'department' => 'required',
            'contact' => 'required',
            'location' => 'required',
            'status' => 'required|in_list[active,inactive]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'dob' => 'required|valid_date',
        ];
        if (!$this->validate($rules)) {
            return redirect()->to('/admin/dashboard')->with('error', $validation->getErrors());
        }
        $data = [
            'employee_id' => $this->request->getPost('employee_id'),
            'name' => $this->request->getPost('name'),
            'designation' => $this->request->getPost('designation'),
            'department' => $this->request->getPost('department'),
            'contact' => $this->request->getPost('contact'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'dob' => $this->request->getPost('dob'),
        ];
        $employeeModel = new \App\Models\EmployeeModel();
        $employeeModel->insert($data);
        $employeeId = $employeeModel->getInsertID();

        // Handle document uploads
        $documentModel = new \App\Models\DocumentModel();
        $now = date('Y-m-d H:i:s');
        // Offer Letter
        $offer = $this->request->getFile('offer_letter');
        if ($offer && $offer->isValid() && !$offer->hasMoved()) {
            $folder = WRITEPATH . 'uploads/offer_letters/';
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            $newName = uniqid('offer_') . '.' . $offer->getExtension();
            $offer->move($folder, $newName);
            $documentModel->insert([
                'employee_id' => $employeeId,
                'doc_type' => 'offer_letter',
                'file_name' => $newName,
                'original_name' => $offer->getClientName(),
                'uploaded_at' => $now
            ]);
        }
        // Experience Certificate
        $exp = $this->request->getFile('experience_certificate');
        if ($exp && $exp->isValid() && !$exp->hasMoved()) {
            $folder = WRITEPATH . 'uploads/experience_certificates/';
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            $newName = uniqid('exp_') . '.' . $exp->getExtension();
            $exp->move($folder, $newName);
            $documentModel->insert([
                'employee_id' => $employeeId,
                'doc_type' => 'experience_certificate',
                'file_name' => $newName,
                'original_name' => $exp->getClientName(),
                'uploaded_at' => $now
            ]);
        }
        // Other Certificates (multiple)
        $others = $this->request->getFileMultiple('other_certificates');
        if ($others) {
            $folder = WRITEPATH . 'uploads/other_certificates/';
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            foreach ($others as $other) {
                if ($other && $other->isValid() && !$other->hasMoved()) {
                    $newName = uniqid('other_') . '.' . $other->getExtension();
                    $other->move($folder, $newName);
                    $documentModel->insert([
                        'employee_id' => $employeeId,
                        'doc_type' => 'other_certificate',
                        'file_name' => $newName,
                        'original_name' => $other->getClientName(),
                        'uploaded_at' => $now
                    ]);
                }
            }
        }
        return redirect()->to('/admin/dashboard')->with('success', 'Employee added successfully.');
    }

    public function viewEmployee($id)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $employeeModel = new \App\Models\EmployeeModel();
        $documentModel = new \App\Models\DocumentModel();
        $employee = $employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Employee not found']);
        }
        $documents = $documentModel->where('employee_id', $id)->findAll();
        return $this->response->setJSON([
            'employee' => $employee,
            'documents' => $documents
        ]);
    }

    public function downloadDocument($docId)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403)->setBody('Unauthorized');
        }
        $documentModel = new \App\Models\DocumentModel();
        $doc = $documentModel->find($docId);
        if (!$doc) {
            return $this->response->setStatusCode(404)->setBody('File not found');
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
        return $this->response->download($filePath, null)->setFileName($doc['original_name']);
    }

    public function getEmployee($id)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Employee not found']);
        }
        return $this->response->setJSON($employee);
    }

    public function updateEmployee($id)
    {
        if (!session()->get('isAdmin')) {
            return redirect()->to('/admin_login');
        }
        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required',
            'designation' => 'required',
            'department' => 'required',
            'contact' => 'required',
            'location' => 'required',
            'status' => 'required|in_list[active,inactive]',
            'start_date' => 'required|valid_date',
            'end_date' => 'permit_empty|valid_date',
            'dob' => 'required|valid_date',
        ];
        if (!$this->validate($rules)) {
            return redirect()->to('/admin/dashboard')->with('error', $validation->getErrors());
        }
        $data = [
            'name' => $this->request->getPost('name'),
            'designation' => $this->request->getPost('designation'),
            'department' => $this->request->getPost('department'),
            'contact' => $this->request->getPost('contact'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'dob' => $this->request->getPost('dob'),
        ];
        $employeeModel = new \App\Models\EmployeeModel();
        $employeeModel->update($id, $data);
        return redirect()->to('/admin/dashboard')->with('success', 'Employee updated successfully.');
    }

    public function approveEmployee($id)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Employee not found']);
        }
        $employeeModel->update($id, ['approved' => 1]);
        return $this->response->setJSON(['success' => true]);
    }

    public function toggleStatus($id)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $employeeModel = new \App\Models\EmployeeModel();
        $employee = $employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Employee not found']);
        }
        $newStatus = $employee['status'] === 'active' ? 'inactive' : 'active';
        $employeeModel->update($id, ['status' => $newStatus]);
        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }

    public function deleteEmployee($id)
    {
        if (!session()->get('isAdmin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $employeeModel = new \App\Models\EmployeeModel();
        $documentModel = new \App\Models\DocumentModel();
        $employee = $employeeModel->find($id);
        if (!$employee) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Employee not found']);
        }
        // Delete documents
        $documents = $documentModel->where('employee_id', $id)->findAll();
        foreach ($documents as $doc) {
            $folder = '';
            if ($doc['doc_type'] === 'offer_letter') {
                $folder = WRITEPATH . 'uploads/offer_letters/';
            } elseif ($doc['doc_type'] === 'experience_certificate') {
                $folder = WRITEPATH . 'uploads/experience_certificates/';
            } else {
                $folder = WRITEPATH . 'uploads/other_certificates/';
            }
            $filePath = $folder . $doc['file_name'];
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
        $documentModel->where('employee_id', $id)->delete();
        $employeeModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin_login');
    }
} 