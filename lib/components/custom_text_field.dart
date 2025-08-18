import 'package:flutter/material.dart';
import '../core/colors.dart';

class CustomTextField extends StatelessWidget {
  const CustomTextField({
    super.key,
    required this.label,
    required this.controller,
    this.onChanged,
    this.keyboardType = TextInputType.text,
    this.isPassword = false,
    this.isPasswordVisible = false,
    this.onPasswordVisibilityChanged,
    this.textDirection,
    this.hintText,
    this.errorText,
    this.prefixIcon,
    this.suffixIcon,
    this.validator,
    this.enabled = true,
    this.maxLines = 1,
    this.textAlign,
    this.contentPadding,
    this.borderRadius = 12.0,
    this.borderColor,
    this.focusedBorderColor,
    this.errorBorderColor,
    this.fillColor,
    this.labelColor,
    this.hintColor,
  });

  final String label;
  final String? hintText;
  final TextEditingController controller;
  final Function(String)? onChanged;
  final TextInputType keyboardType;
  final bool isPassword;
  final bool isPasswordVisible;
  final VoidCallback? onPasswordVisibilityChanged;
  final TextDirection? textDirection;
  final Widget? prefixIcon;
  final Widget? suffixIcon;
  final String? Function(String?)? validator;
  final bool enabled;
  final int maxLines;
  final TextAlign? textAlign;
  final EdgeInsetsGeometry? contentPadding;
  final double borderRadius;
  final Color? borderColor;
  final Color? focusedBorderColor;
  final Color? errorBorderColor;
  final Color? fillColor;
  final Color? labelColor;
  final Color? hintColor;
  final String? errorText;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (label.isNotEmpty)
          Padding(
            padding: const EdgeInsets.only(bottom: 8.0),
            child: Text(
              label,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: labelColor ?? Colors.grey[700],
              ),
            ),
          ),
        TextFormField(
          controller: controller,
          onChanged: onChanged,
          keyboardType: keyboardType,
          obscureText: isPassword && !isPasswordVisible,
          textDirection: textDirection,
          validator: validator,
          enabled: enabled,
          maxLines: maxLines,
          textAlign: textAlign ?? TextAlign.start,
          decoration: InputDecoration(
            hintText: hintText ?? label,
            hintStyle: TextStyle(
              color: hintColor ?? Colors.grey[400],
              fontSize: 16,
            ),
            prefixIcon: prefixIcon,
            suffixIcon: _buildSuffixIcon(),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(borderRadius),
              borderSide: BorderSide(color: borderColor ?? Colors.grey[300]!),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(borderRadius),
              borderSide: BorderSide(color: borderColor ?? Colors.grey[300]!),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(borderRadius),
              borderSide: BorderSide(
                color: focusedBorderColor ?? AppColors.primary,
                width: 2,
              ),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(borderRadius),
              borderSide: BorderSide(
                color: errorBorderColor ?? Colors.red[400]!,
              ),
            ),
            filled: fillColor != null,
            fillColor: fillColor,
            contentPadding:
                contentPadding ??
                const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            errorText: errorText,
          ),
        ),
      ],
    );
  }

  Widget? _buildSuffixIcon() {
    if (isPassword) {
      return IconButton(
        icon: Icon(
          isPasswordVisible ? Icons.visibility : Icons.visibility_off,
          color: Colors.grey[600],
        ),
        onPressed: onPasswordVisibilityChanged,
      );
    }
    return suffixIcon;
  }
}
