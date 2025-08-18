import 'package:flutter/material.dart';
import 'custom_text.dart';
import '../core/colors.dart';

class SuccessMessage extends StatelessWidget {
  const SuccessMessage({
    super.key,
    required this.message,
    this.onClose,
    this.backgroundColor,
    this.borderColor,
    this.textColor,
    this.icon,
    this.showCloseButton = true,
  });

  final String message;
  final VoidCallback? onClose;
  final Color? backgroundColor;
  final Color? borderColor;
  final Color? textColor;
  final Widget? icon;
  final bool showCloseButton;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: backgroundColor ?? Colors.green[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: borderColor ?? AppColors.primary),
      ),
      child: Row(
        children: [
          icon ?? Icon(Icons.check_circle_outline, color: AppColors.primary),
          const SizedBox(width: 12),
          Expanded(
            child: CustomText(
              text: message,
              color: textColor ?? AppColors.primary,
              size: 14,
              weight: FontWeight.w600,
            ),
          ),
          if (showCloseButton && onClose != null)
            IconButton(
              icon: Icon(Icons.close, color: AppColors.primary),
              onPressed: onClose,
              iconSize: 20,
            ),
        ],
      ),
    );
  }
}
