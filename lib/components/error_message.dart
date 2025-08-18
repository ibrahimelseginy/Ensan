import 'package:flutter/material.dart';
import 'custom_text.dart';

class ErrorMessage extends StatelessWidget {
  const ErrorMessage({
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
        color: backgroundColor ?? Colors.red[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: borderColor ?? Colors.red[200]!),
      ),
      child: Row(
        children: [
          icon ?? Icon(Icons.error_outline, color: Colors.red[600]),
          const SizedBox(width: 12),
          Expanded(
            child: CustomText(
              text: message,
              color: textColor ?? Colors.red[700]!,
              size: 14,
              weight: FontWeight.w600,
            ),
          ),
          if (showCloseButton && onClose != null)
            IconButton(
              icon: Icon(Icons.close, color: Colors.red[600]),
              onPressed: onClose,
              iconSize: 20,
            ),
        ],
      ),
    );
  }
}
