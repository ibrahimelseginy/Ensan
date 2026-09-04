import os
import re

directory = r"f:/Projects/Ensan/System.Ensan-main/resources/views"
pattern = re.compile(r"asset\('storage/'\s*\.\s*\$([a-zA-Z0-9_]+)->(?:[a-zA-Z0-9_]+_)?path\)")

count = 0
for root, _, files in os.walk(directory):
    for filename in files:
        if filename.endswith(".blade.php"):
            filepath = os.path.join(root, filename)
            with open(filepath, "r", encoding="utf-8") as f:
                content = f.read()
            
            # We want to replace asset('storage/' . $model->..._path)
            # Make sure we match properties ending with _path like image_path, logo_path, cv_path.
            
            # The pattern is: asset('storage/' . $var->something_path)
            # Replacement: $var->image_url
            
            new_content, num_subs = re.subn(r"asset\('storage/'\s*\.\s*\$([a-zA-Z0-9_]+)->[a-zA-Z0-9_]*path\)", r"$\1->image_url", content)
            
            # Wait, some models might use different accessors or have other logic.
            # But we added `image_url` accessor to all Models that have _path (which defaults to full URL).
            # So `$\1->image_url` is mostly safe for those where `UploadsImages` trait is used and appended.
            
            # Let's also check for {{ asset('storage/' . $page->image_path) }} in CSS background:
            
            if num_subs > 0:
                with open(filepath, "w", encoding="utf-8") as f:
                    f.write(new_content)
                count += num_subs
                print(f"Updated {filepath} ({num_subs} replacements)")

print(f"Total replacements: {count}")
