<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f8fafc; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem 1rem 1rem;
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            z-index: 100;
        }
        .sidebar .logo {
            max-width: 120px;
            max-height: 80px;
            margin-bottom: 2rem;
        }
        .sidebar .nav-link {
            color: #fff;
            font-weight: 500;
            margin: 1rem 0;
            border-radius: 0.5rem;
            transition: background 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
        }
        .sidebar .logout-link {
            margin-top: auto;
            color: #fff;
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.10);
            text-align: center;
            width: 100%;
            transition: background 0.2s;
            text-decoration: none;
        }
        .sidebar .logout-link:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }
        .dashboard-navbar {
            position: fixed;
            left: 240px;
            right: 0;
            top: 0;
            height: 64px;
            background: linear-gradient(90deg, #1e293b 0%, #2563eb 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 101;
            box-shadow: 0 2px 8px rgba(30,41,59,0.08);
        }
        .dashboard-navbar .navbar-title {
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .dashboard-navbar .navbar-user {
            font-size: 1.5rem;
        }
        .main-content {
            margin-left: 240px;
            padding: 2rem 1rem 1rem 1rem;
            padding-top: 80px;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .table thead { background: #6366f1; color: #fff; }
        .table-striped>tbody>tr:nth-of-type(odd) { background-color: #eef2ff; }
        .add-employee-btn {
            background: linear-gradient(90deg, #6366f1 0%, #60a5fa 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1.2rem;
            transition: background 0.2s;
        }
        .add-employee-btn:hover {
            background: linear-gradient(90deg, #60a5fa 0%, #6366f1 100%);
        }
        @media (max-width: 991.98px) {
            .sidebar {
                width: 100vw;
                min-height: auto;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 1rem;
                position: static;
            }
            .dashboard-navbar {
                left: 0;
                padding: 0 1rem;
            }
            .main-content {
                margin-left: 0;
                margin-top: 80px;
                padding: 1rem;
            }
        }
        @media (max-width: 575.98px) {
            .sidebar {
                flex-direction: column;
                align-items: center;
                padding: 1rem 0.5rem;
            }
            .dashboard-navbar {
                height: 56px;
                font-size: 1rem;
                padding: 0 0.5rem;
            }
            .main-content {
                margin-top: 100px;
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar shadow">
        <img src="/smec_white.png" alt="SMEC Logo" class="logo">
        <nav class="nav flex-column w-100 mt-2">
            <a class="nav-link active" href="#">Employee List</a>
            <!-- Add more nav links here if needed -->
        </nav>
        <a href="<?= site_url('/admin/logout') ?>" class="logout-link mt-auto">Logout</a>
    </div>
    <div class="dashboard-navbar">
        <span class="navbar-title"><i class="bi bi-speedometer2"></i> Admin Dashboard</span>
        <span class="navbar-user"><i class="bi bi-person-circle"></i></span>
    </div>
    <div class="main-content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                $errors = session()->getFlashdata('error');
                if (is_array($errors)) {
                    foreach ($errors as $err) echo esc($err) . '<br>';
                } else {
                    echo esc($errors);
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="dashboard-header">
            <h2>Employee List</h2>
            <button class="add-employee-btn" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">+ Add Employee</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees) && is_array($employees)): ?>
                        <?php foreach ($employees as $i => $emp): ?>
                            <tr>
                                <td><?= esc(($pager->getCurrentPage() - 1) * $pager->getPerPage() + $i + 1) ?></td>
                                <td><?= esc($emp['employee_id']) ?></td>
                                <td><?= esc($emp['name']) ?></td>
                                <td><?= esc($emp['designation']) ?></td>
                                <td><?= esc($emp['department']) ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-sm btn-info text-white view-employee-btn" data-emp-id="<?= esc($emp['id']) ?>" title="View" data-bs-toggle="tooltip" data-bs-placement="top"><i class="bi bi-eye"></i> View</button>
                                        <button class="btn btn-sm btn-warning text-dark edit-employee-btn" data-emp-id="<?= esc($emp['id']) ?>" title="Edit" data-bs-toggle="tooltip" data-bs-placement="top"><i class="bi bi-pencil"></i> Edit</button>
                                        <?php if ($emp['approved']): ?>
                                          <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                                        <?php else: ?>
                                          <button class="btn btn-sm btn-success approve-employee-btn" data-emp-id="<?= esc($emp['id']) ?>" title="Approve" data-bs-toggle="tooltip" data-bs-placement="top"><i class="bi bi-check-circle"></i> Approve</button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm toggle-status-btn <?= $emp['status'] === 'active' ? 'btn-secondary' : 'btn-success' ?>" data-emp-id="<?= esc($emp['id']) ?>" data-status="<?= esc($emp['status']) ?>" title="<?= $emp['status'] === 'active' ? 'Deactivate' : 'Activate' ?>" data-bs-toggle="tooltip" data-bs-placement="top">
                                          <i class="bi <?= $emp['status'] === 'active' ? 'bi-x-circle' : 'bi-check-circle' ?>"></i> <?= $emp['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-employee-btn" data-emp-id="<?= esc($emp['id']) ?>" title="Delete" data-bs-toggle="tooltip" data-bs-placement="top"><i class="bi bi-trash"></i> Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No employees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <?= $pager->links() ?>
        </div>
    </div>
    <!-- Add Employee Modal (to be implemented) -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header bg-primary text-white rounded-top-4">
            <h5 class="modal-title d-flex align-items-center gap-2" id="addEmployeeModalLabel">
              <i class="bi bi-person-plus"></i> Add Employee
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body bg-light rounded-bottom-4">
            <form id="addEmployeeForm" method="post" action="<?= site_url('/admin/add_employee') ?>" enctype="multipart/form-data">
                <h6 class="mb-3 text-primary"><i class="bi bi-person-badge"></i> Employee Details</h6>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label for="employee_id" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="designation" class="form-label">Designation</label>
                    <input type="text" class="form-control" id="designation" name="designation" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" name="department" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                  </div>
                </div>
                <hr class="my-4">
                <h6 class="mb-3 text-primary"><i class="bi bi-upload"></i> Upload Documents</h6>
                <div class="mb-3">
                  <label for="offer_letter" class="form-label"><i class="bi bi-file-earmark-arrow-up"></i> Offer Letter</label>
                  <input type="file" class="form-control" id="offer_letter" name="offer_letter" accept=".pdf,.doc,.docx,.jpeg,.jpg,.png">
                </div>
                <div class="mb-3">
                  <label for="experience_certificate" class="form-label"><i class="bi bi-file-earmark-arrow-up"></i> Experience Certificate</label>
                  <input type="file" class="form-control" id="experience_certificate" name="experience_certificate" accept=".pdf,.doc,.docx,.jpeg,.jpg,.png">
                </div>
                <div class="mb-3">
                  <label for="other_certificates" class="form-label"><i class="bi bi-files"></i> Other Certificates</label>
                  <input type="file" class="form-control" id="other_certificates" name="other_certificates[]" multiple accept=".pdf,.doc,.docx,.jpeg,.jpg,.png">
                </div>
                <div class="d-grid mt-4">
                  <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-person-plus"></i> Add Employee</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header bg-info text-white rounded-top-4">
            <h5 class="modal-title d-flex align-items-center gap-2" id="viewEmployeeModalLabel">
              <i class="bi bi-person-lines-fill"></i> Employee Details
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body bg-light rounded-bottom-4">
            <div id="view-employee-content">
              <div class="text-center text-muted py-5">
                <div class="spinner-border text-info" role="status"></div>
                <div>Loading...</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header bg-warning text-dark rounded-top-4">
            <h5 class="modal-title d-flex align-items-center gap-2" id="editEmployeeModalLabel">
              <i class="bi bi-pencil-square"></i> Edit Employee
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body bg-light rounded-bottom-4">
            <form id="editEmployeeForm" method="post">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label for="edit_employee_id" class="form-label">Employee ID</label>
                  <input type="text" class="form-control" id="edit_employee_id" name="employee_id" disabled>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_designation" class="form-label">Designation</label>
                  <input type="text" class="form-control" id="edit_designation" name="designation" required>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_department" class="form-label">Department</label>
                  <input type="text" class="form-control" id="edit_department" name="department" required>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_contact" class="form-label">Contact</label>
                  <input type="text" class="form-control" id="edit_contact" name="contact" required>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_location" class="form-label">Location</label>
                  <input type="text" class="form-control" id="edit_location" name="location" required>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_status" class="form-label">Status</label>
                  <select class="form-select" id="edit_status" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_start_date" class="form-label">Start Date</label>
                  <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_end_date" class="form-label">End Date</label>
                  <input type="date" class="form-control" id="edit_end_date" name="end_date">
                </div>
                <div class="col-12 col-md-6">
                  <label for="edit_dob" class="form-label">Date of Birth</label>
                  <input type="date" class="form-control" id="edit_dob" name="dob" required>
                </div>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-warning btn-lg text-dark"><i class="bi bi-pencil-square"></i> Update Employee</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    <script>
document.querySelectorAll('.view-employee-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const empId = this.getAttribute('data-emp-id');
    const modal = new bootstrap.Modal(document.getElementById('viewEmployeeModal'));
    const content = document.getElementById('view-employee-content');
    content.innerHTML = `<div class='text-center text-muted py-5'><div class='spinner-border text-info' role='status'></div><div>Loading...</div></div>`;
    modal.show();
    fetch(`<?= site_url('/admin/view_employee/') ?>${empId}`)
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          content.innerHTML = `<div class='alert alert-danger'>${data.error}</div>`;
          return;
        }
        const emp = data.employee;
        let html = `<div class='row g-4'>`;
        html += `<div class='col-md-6'><h6 class='text-primary'>Personal Details</h6><ul class='list-group list-group-flush'>`;
        html += `<li class='list-group-item'><strong>Employee ID:</strong> ${emp.employee_id}</li>`;
        html += `<li class='list-group-item'><strong>Name:</strong> ${emp.name}</li>`;
        html += `<li class='list-group-item'><strong>Designation:</strong> ${emp.designation}</li>`;
        html += `<li class='list-group-item'><strong>Department:</strong> ${emp.department}</li>`;
        html += `<li class='list-group-item'><strong>Contact:</strong> ${emp.contact}</li>`;
        html += `<li class='list-group-item'><strong>Location:</strong> ${emp.location}</li>`;
        html += `<li class='list-group-item'><strong>Status:</strong> <span class='badge ${emp.status === 'active' ? 'bg-success' : 'bg-secondary'}'>${emp.status.charAt(0).toUpperCase() + emp.status.slice(1)}</span></li>`;
        html += `<li class='list-group-item'><strong>Start Date:</strong> ${emp.start_date}</li>`;
        let endDateDisplay = (emp.end_date && emp.end_date !== '0000-00-00' && emp.end_date !== '0-00-0000') ? emp.end_date : '<span class="text-success">Currently Working</span>';
        html += `<li class='list-group-item'><strong>End Date:</strong> ${endDateDisplay}</li>`;
        html += `<li class='list-group-item'><strong>Date of Birth:</strong> ${emp.dob}</li>`;
        html += `</ul></div>`;
        html += `<div class='col-md-6'><h6 class='text-primary'>Documents</h6>`;
        if (data.documents.length === 0) {
          html += `<div class='alert alert-warning'>No documents uploaded.</div>`;
        } else {
          html += `<div class='row g-2'>`;
          data.documents.forEach(doc => {
            let thumb = '';
            const ext = doc.file_name.split('.').pop().toLowerCase();
            if (["jpg","jpeg","png"].includes(ext)) {
              thumb = `<img src='<?= site_url('/admin/download/') ?>${doc.id}' class='img-thumbnail' style='max-width:80px;max-height:80px;' alt='${doc.original_name}'>`;
            } else if (["pdf"].includes(ext)) {
              thumb = `<span class='display-4 text-danger'><i class='bi bi-file-earmark-pdf'></i></span>`;
            } else if (["doc","docx"].includes(ext)) {
              thumb = `<span class='display-4 text-primary'><i class='bi bi-file-earmark-word'></i></span>`;
            } else {
              thumb = `<span class='display-4'><i class='bi bi-file-earmark'></i></span>`;
            }
            html += `<div class='col-6 col-lg-4 text-center mb-2'>${thumb}<div class='small mt-1'>${doc.doc_type.replace('_',' ').replace(/\b\w/g, l => l.toUpperCase())}</div><a href='<?= site_url('/admin/download/') ?>${doc.id}' class='btn btn-outline-primary btn-sm mt-1'><i class='bi bi-download'></i> Download</a></div>`;
          });
          html += `</div>`;
        }
        html += `</div></div>`;
        content.innerHTML = html;
      })
      .catch(() => {
        content.innerHTML = `<div class='alert alert-danger'>Failed to load employee details.</div>`;
      });
  });
});
</script>
<script>
document.querySelectorAll('.edit-employee-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const empId = this.getAttribute('data-emp-id');
    const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
    fetch(`<?= site_url('/admin/get_employee/') ?>${empId}`)
      .then(res => res.json())
      .then(emp => {
        document.getElementById('edit_employee_id').value = emp.employee_id;
        document.getElementById('edit_name').value = emp.name;
        document.getElementById('edit_designation').value = emp.designation;
        document.getElementById('edit_department').value = emp.department;
        document.getElementById('edit_contact').value = emp.contact;
        document.getElementById('edit_location').value = emp.location;
        document.getElementById('edit_status').value = emp.status;
        document.getElementById('edit_start_date').value = emp.start_date;
        document.getElementById('edit_end_date').value = emp.end_date;
        document.getElementById('edit_dob').value = emp.dob;
        document.getElementById('editEmployeeForm').setAttribute('action', `<?= site_url('/admin/update_employee/') ?>${emp.id}`);
        modal.show();
      });
  });
});
</script>
<script>
document.querySelectorAll('.approve-employee-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const empId = this.getAttribute('data-emp-id');
    fetch(`<?= site_url('/admin/approve_employee/') ?>${empId}`, { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          btn.outerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>';
        } else {
          alert(data.error || 'Failed to approve employee.');
        }
      });
  });
});
</script>
<script>
document.querySelectorAll('.toggle-status-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const empId = this.getAttribute('data-emp-id');
    const button = this;
    fetch(`<?= site_url('/admin/toggle_status/') ?>${empId}`, { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          if (data.status === 'active') {
            button.className = 'btn btn-sm btn-secondary toggle-status-btn';
            button.innerHTML = '<i class="bi bi-x-circle"></i> Deactivate';
            button.setAttribute('title', 'Deactivate');
            button.setAttribute('data-status', 'active');
          } else {
            button.className = 'btn btn-sm btn-success toggle-status-btn';
            button.innerHTML = '<i class="bi bi-check-circle"></i> Activate';
            button.setAttribute('title', 'Activate');
            button.setAttribute('data-status', 'inactive');
          }
        } else {
          alert(data.error || 'Failed to change status.');
        }
      });
  });
});
</script>
<script>
document.querySelectorAll('.delete-employee-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const empId = this.getAttribute('data-emp-id');
    if (!confirm('Are you sure you want to delete this employee and all their documents?')) return;
    const row = btn.closest('tr');
    fetch(`<?= site_url('/admin/delete_employee/') ?>${empId}`, { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          row.remove();
        } else {
          alert(data.error || 'Failed to delete employee.');
        }
      });
  });
});
</script>
</body>
</html> 