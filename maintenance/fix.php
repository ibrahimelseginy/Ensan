<?php
$file = 'f:/Projects/Ensan/System.Ensan-main/app/Http/Controllers/ProjectWebController.php';
$content = file_get_contents($file);

$target = <<<'PHP'
    // Zad Management
    public function storeZadFamily(Request $request, Project $project)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'mother_name' => 'nullable|string',
            'children_names' => 'nullable|string',
            'phone' => 'nullable|string',
            'backup_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'children_count' => 'nullable|integer',
            'poultry_type' => 'nullable|string',
            'notes_cases' => 'nullable|string',
            'meat' => 'nullable|string'
        ]);

        $data['project_id'] = $project->id;
        $data['assistance_type'] = 'zad'; // Mark as Zad assistance
        
        Beneficiary::create($data);
        return back()->with('success', 'تم إضافة حالة أهالي زاد بنجاح');
    }
PHP;

$replacement = <<<'PHP'
    // Zad Management
    public function storeZadFamily(Request $request, Project $project)
    {
        $data = $request->validate([
            'mother_name' => 'required|string',
            'children_names' => 'nullable|string',
            'phone' => 'nullable|string',
            'backup_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'children_count' => 'nullable|integer',
            'sponsored_children_count' => 'nullable|integer',
            'study_grade' => 'nullable|string',
            'poultry_type' => 'nullable|string',
            'notes_cases' => 'nullable|string',
            'meat' => 'nullable|string'
        ]);

        $data['full_name'] = $data['mother_name']; // Map mother_name to full_name to satisfy Beneficiary model requirements
        $data['project_id'] = $project->id;
        $data['assistance_type'] = 'zad'; // Mark as Zad assistance
        
        \App\Models\Beneficiary::create($data);
        return back()->with('success', 'تم إضافة حالة أهالي زاد بنجاح');
    }
PHP;

if (strpos($content, $target) !== false) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($file, $content);
    echo "Replaced successfully\n";
}
else {
    echo "Target string not found in the file\n";
}
