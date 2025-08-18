import 'package:ensan_test/components/custom_text.dart';
import 'package:ensan_test/core/colors.dart';
import 'package:flutter/material.dart';

class CustomAppbar extends StatelessWidget implements PreferredSizeWidget {
  const CustomAppbar({
    super.key,
    required this.isArabic,
    required this.onLanguageChanged,
    this.title,
    this.showLanguageToggle = true,
  });

  final bool isArabic;
  final Function(bool) onLanguageChanged;
  final String? title;
  final bool showLanguageToggle;

  @override
  Size get preferredSize => const Size.fromHeight(80);

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: AppColors.primary.withOpacity(0.9),
      title: title != null
          ? CustomText(
              text: title!,
              size: 20,
              weight: FontWeight.bold,
              color: Colors.white,
            )
          : null,
      // عرض زر تبديل اللغة في الجانب الأيمن للعربية والجانب الأيسر للإنجليزية
      leading: (!isArabic && showLanguageToggle)
          ? Padding(
              padding: const EdgeInsets.only(left: 16.0, top: 8.0),
              child: GestureDetector(
                onTap: () => onLanguageChanged(!isArabic),
                child: Container(
                  width: 50,
                  height: 50,
                  decoration: BoxDecoration(
                    color: const Color.fromARGB(255, 212, 212, 212),
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: CustomText(
                      text: "ع",
                      size: 18,
                      weight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            )
          : null,
      actions: (isArabic && showLanguageToggle)
          ? [
              Padding(
                padding: const EdgeInsets.only(right: 16.0, top: 8.0),
                child: GestureDetector(
                  onTap: () => onLanguageChanged(!isArabic),
                  child: Container(
                    width: 50,
                    height: 50,
                    decoration: BoxDecoration(
                      color: const Color.fromARGB(255, 212, 212, 212),
                      shape: BoxShape.circle,
                    ),
                    child: Center(
                      child: CustomText(
                        text: "en",
                        size: 18,
                        weight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ),
              ),
            ]
          : null,
      leadingWidth: (!isArabic && showLanguageToggle) ? 80 : 25,
      centerTitle: true,
      scrolledUnderElevation: 0.0,
    );
  }
}
