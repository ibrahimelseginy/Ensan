import 'package:flutter/material.dart';
import 'colors.dart';
import 'app_constants.dart';

class AppStyles {
  // Text styles
  static const TextStyle headingStyle = TextStyle(
    fontSize: AppConstants.titleTextSize,
    fontWeight: FontWeight.bold,
    color: Colors.white,
  );

  static const TextStyle subheadingStyle = TextStyle(
    fontSize: AppConstants.largeTextSize,
    fontWeight: FontWeight.w800,
    color: Colors.white70,
  );

  static const TextStyle bodyStyle = TextStyle(
    fontSize: AppConstants.defaultTextSize,
    fontWeight: FontWeight.normal,
    color: Colors.black87,
  );

  static const TextStyle errorStyle = TextStyle(
    fontSize: AppConstants.smallTextSize,
    fontWeight: FontWeight.w600,
    color: Colors.red,
  );

  // Input decoration styles
  static InputDecoration getInputDecoration({
    required String label,
    String? hint,
    Widget? prefixIcon,
    Widget? suffixIcon,
    String? errorText,
    bool isFocused = false,
  }) {
    return InputDecoration(
      labelText: label,
      hintText: hint,
      prefixIcon: prefixIcon,
      suffixIcon: suffixIcon,
      errorText: errorText,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
        borderSide: const BorderSide(color: Colors.grey),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
        borderSide: const BorderSide(color: Colors.grey),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
        borderSide: const BorderSide(color: AppColors.primary, width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.defaultBorderRadius),
        borderSide: const BorderSide(color: Colors.red),
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
    );
  }

  // Button styles
  static ButtonStyle getPrimaryButtonStyle({
    Color? backgroundColor,
    double? borderRadius,
    double? elevation,
  }) {
    return ElevatedButton.styleFrom(
      backgroundColor: backgroundColor ?? AppColors.primary,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(
          borderRadius ?? AppConstants.defaultBorderRadius,
        ),
      ),
      elevation: elevation ?? 0,
      padding: const EdgeInsets.symmetric(
        horizontal: AppConstants.defaultPadding,
        vertical: AppConstants.smallPadding,
      ),
    );
  }
}
