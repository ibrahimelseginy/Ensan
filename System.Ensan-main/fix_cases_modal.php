<?php
$file = __DIR__ . '/resources/views/mobile/cases.blade.php';
$content = file_get_contents($file);

// Find the start of the modal
$modalStart = strpos($content, '{{-- Case Detail Modal --}}');
$modalEnd = strpos($content, '</div>', strpos($content, '</div>', strpos($content, '</div>', strpos($content, '</div>', strpos($content, '</div>', strpos($content, '</div>', strpos($content, '</div>', strpos($content, '<div class="modal fade" id="viewCase{{ $case->id }}"'))))))));
// Let's use regex to extract the modal block
$pattern = '/\{\{-- Case Detail Modal --\}\}.*?(?:(?=^\s*@empty)|(?=^\s*@endforelse)|(?=^\s*<\/tbody>))/ms';

if (preg_match($pattern, $content, $matches)) {
    $modalContent = $matches[0];
    
    // Remove it from the loop
    $content = str_replace($modalContent, '', $content);
    
    // We need to loop again at the end of the file for the modals
    $modalsLoop = "
@foreach(\$applications as \$case)
" . $modalContent . "
@endforeach
";
    
    $content = str_replace('@endsection', $modalsLoop . "\n@endsection", $content);
    file_put_contents($file, $content);
    echo "Modal moved successfully.\n";
} else {
    echo "Modal not found with regex.\n";
}
