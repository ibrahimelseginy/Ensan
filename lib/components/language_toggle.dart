import 'package:flutter/material.dart';
import 'custom_text.dart';
import '../core/colors.dart';

class LanguageToggle extends StatelessWidget {
  const LanguageToggle({
    super.key,
    required this.isArabic,
    required this.onLanguageChanged,
    this.size = const Size(42, 35),
    this.backgroundColor,
    this.textColor,
  });

  final bool isArabic;
  final Function(bool) onLanguageChanged;
  final Size size;
  final Color? backgroundColor;
  final Color? textColor;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => onLanguageChanged(!isArabic),
      child: Container(
        width: size.width,
        height: size.height,
        decoration: BoxDecoration(
          color: backgroundColor ?? AppColors.primary,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Center(
          child: CustomText(
            text: isArabic ? "en" : "ع",
            size: 18,
            weight: FontWeight.bold,
            color: textColor ?? Colors.white,
          ),
        ),
      ),
    );
  }
}
