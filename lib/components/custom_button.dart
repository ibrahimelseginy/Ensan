import 'package:flutter/material.dart';
import 'package:gap/gap.dart';
import 'custom_text.dart';
import '../core/colors.dart';

class CustomButton extends StatelessWidget {
  const CustomButton({
    super.key,
    required this.title,
    required this.onTap,
    this.isLoading = false,
    this.icon,
    this.backgroundColor,
    this.textColor,
    this.height = 56,
  });

  final String title;
  final VoidCallback onTap;
  final bool isLoading;
  final Widget? icon;
  final Color? backgroundColor;
  final Color? textColor;
  final double height;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: isLoading ? null : onTap,
      child: Container(
        height: height,
        decoration: BoxDecoration(color: backgroundColor ?? Color(0xFF00695C)),
        child: Center(
          child: isLoading
              ? const SizedBox(
                  width: 24,
                  height: 24,
                  child: CircularProgressIndicator(
                    strokeWidth: 2,
                    valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                  ),
                )
              : Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    if (icon != null) ...[icon!, const Gap(10)],
                    CustomText(
                      text: title.toUpperCase(),
                      size: 18,
                      color: textColor ?? Colors.white,
                      weight: FontWeight.w600,
                    ),
                  ],
                ),
        ),
      ),
    );
  }
}
