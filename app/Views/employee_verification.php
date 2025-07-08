<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Verification - SMEC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f8fafc; }
        .logo {
            max-width: 140px;
            margin: 2rem auto 1rem auto;
            display: block;
        }
        .search-section, .emp-card, .doc-section {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(30,41,59,0.08);
            padding: 2rem 2.5rem;
            max-width: 600px;
            margin: 2rem auto 2rem auto;
        }
        .emp-card, .doc-section {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .section-header {
            background: #e3f0fc;
            border-radius: 1rem 1rem 0 0;
            padding: 1rem 2rem;
            margin: -2rem -2.5rem 2rem -2.5rem;
            font-weight: 600;
            font-size: 1.25rem;
            color: #2563eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .doc-thumb {
            max-width: 80px;
            max-height: 80px;
        }
        .footer {
            text-align: center;
            color: #888;
            margin-top: 3rem;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <a href="/">
        <img src="/smec_logo.png" alt="SMEC Logo" class="logo">
    </a>
    <div class="search-section">
        <div class="section-header justify-content-center"><i class="bi bi-search"></i> Employee Verification</div>
        <form method="get" action="/">
            <div class="mb-3">
                <label for="employee_id" class="form-label">Employee ID</label>
                <input type="text" class="form-control" id="employee_id" name="employee_id" required value="<?= esc($_GET['employee_id'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" required value="<?= esc($_GET['dob'] ?? '') ?>">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-search"></i> Search</button>
                <a href="/" class="btn btn-secondary btn-lg">Reset</a>
            </div>
        </form>
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger mt-3 text-center"><?= esc($error) ?></div>
        <?php endif; ?>
    </div>
    <?php if (isset($employee) && $employee): ?>
    <div class="emp-card">
        <div class="section-header"><i class="bi bi-person-badge"></i> Employee Details</div>
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item"><strong>Employee ID:</strong> <?= esc($employee['employee_id']) ?></li>
                    <li class="list-group-item"><strong>Name:</strong> <?= esc($employee['name']) ?></li>
                    <li class="list-group-item"><strong>Designation:</strong> <?= esc($employee['designation']) ?></li>
                    <li class="list-group-item"><strong>Department:</strong> <?= esc($employee['department']) ?></li>
                    <li class="list-group-item"><strong>Contact:</strong> <?= esc($employee['contact']) ?></li>
                    <li class="list-group-item"><strong>Location:</strong> <?= esc($employee['location']) ?></li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item"><strong>Status:</strong> <span class="badge <?= $employee['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst(esc($employee['status'])) ?></span></li>
                    <li class="list-group-item"><strong>Start Date:</strong> <?= esc($employee['start_date']) ?></li>
                    <li class="list-group-item"><strong>End Date:</strong> <?= ($employee['end_date'] && $employee['end_date'] !== '0000-00-00' && $employee['end_date'] !== '0-00-0000') ? esc($employee['end_date']) : '<span class=\'text-success\'>Currently Working</span>' ?></li>
                    <li class="list-group-item"><strong>Date of Birth:</strong> <?= esc($employee['dob']) ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="doc-section">
        <div class="section-header"><i class="bi bi-files"></i> Documents</div>
        <?php
        $docModel = new \App\Models\DocumentModel();
        $docs = $docModel->where('employee_id', $employee['id'])->findAll();
        ?>
        <?php if (empty($docs)): ?>
            <div class="alert alert-warning">No documents uploaded.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($docs as $doc): ?>
                    <?php
                    $ext = strtolower(pathinfo($doc['file_name'], PATHINFO_EXTENSION));
                    $downloadUrl = site_url('/public/download/' . $doc['id']);
                    ?>
                    <div class="col-6 col-md-4 text-center mb-2">
                        <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                            <img src="<?= $downloadUrl ?>" class="img-thumbnail doc-thumb" alt="<?= esc($doc['original_name']) ?>">
                        <?php elseif ($ext === 'pdf'): ?>
                            <span class="display-4 text-danger"><i class="bi bi-file-earmark-pdf"></i></span>
                        <?php elseif (in_array($ext, ['doc','docx'])): ?>
                            <span class="display-4 text-primary"><i class="bi bi-file-earmark-word"></i></span>
                        <?php else: ?>
                            <span class="display-4"><i class="bi bi-file-earmark"></i></span>
                        <?php endif; ?>
                        <div class="small mt-1"> <?= ucfirst(str_replace('_',' ', $doc['doc_type'])) ?> </div>
                        <a href="<?= $downloadUrl ?>" class="btn btn-outline-primary btn-sm mt-1" download><i class="bi bi-download"></i> Download</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="footer">
        &copy; <span id="copyright-year"></span> All rights reserved SMEC
    </div>
    <script>
        document.getElementById('copyright-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>
