import 'package:flutter/material.dart';
import 'colors.dart';
import 'constants/app_constants.dart';

class AppStyles {
  // Text styles
  static const TextStyle headingStyle = TextStyle(
    fontSize: AppConstants.titleText,
    fontWeight: FontWeight.bold,
    color: AppColors.textPrimary,
  );

  static const TextStyle subheadingStyle = TextStyle(
    fontSize: AppConstants.largeText,
    fontWeight: FontWeight.w800,
    color: AppColors.textSecondary,
  );

  static const TextStyle bodyStyle = TextStyle(
    fontSize: AppConstants.defaultText,
    fontWeight: FontWeight.normal,
    color: AppColors.textPrimary,
  );

  static const TextStyle errorStyle = TextStyle(
    fontSize: AppConstants.smallText,
    fontWeight: FontWeight.w600,
    color: AppColors.error,
  );
  
  static const TextStyle successStyle = TextStyle(
    fontSize: AppConstants.smallText,
    fontWeight: FontWeight.w600,
    color: AppColors.success,
  );
  
  static const TextStyle captionStyle = TextStyle(
    fontSize: AppConstants.smallText,
    fontWeight: FontWeight.normal,
    color: AppColors.textSecondary,
  );
  
  static const TextStyle buttonTextStyle = TextStyle(
    fontSize: AppConstants.mdText,
    fontWeight: FontWeight.w600,
    color: AppColors.white,
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
        borderRadius: BorderRadius.circular(AppConstants.mdRadius),
        borderSide: const BorderSide(color: AppColors.border),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.mdRadius),
        borderSide: const BorderSide(color: AppColors.border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.mdRadius),
        borderSide: const BorderSide(color: AppColors.primary, width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppConstants.mdRadius),
        borderSide: const BorderSide(color: AppColors.error),
      ),
      contentPadding: const EdgeInsets.symmetric(
        horizontal: AppConstants.mdPadding, 
        vertical: AppConstants.mdPadding
      ),
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
          borderRadius ?? AppConstants.mdRadius,
        ),
      ),
      elevation: elevation ?? AppConstants.xsElevation,
      padding: const EdgeInsets.symmetric(
        horizontal: AppConstants.mdPadding,
        vertical: AppConstants.smPadding,
      ),
    );
  }
  
  static ButtonStyle getSecondaryButtonStyle({
    Color? backgroundColor,
    double? borderRadius,
    double? elevation,
  }) {
    return ElevatedButton.styleFrom(
      backgroundColor: backgroundColor ?? AppColors.secondary,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(
          borderRadius ?? AppConstants.mdRadius,
        ),
      ),
      elevation: elevation ?? AppConstants.xsElevation,
      padding: const EdgeInsets.symmetric(
        horizontal: AppConstants.mdPadding,
        vertical: AppConstants.smPadding,
      ),
    );
  }
  
  static ButtonStyle getOutlinedButtonStyle({
    Color? borderColor,
    double? borderRadius,
  }) {
    return OutlinedButton.styleFrom(
      side: BorderSide(
        color: borderColor ?? AppColors.primary,
        width: 1.5,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(
          borderRadius ?? AppConstants.mdRadius,
        ),
      ),
      padding: const EdgeInsets.symmetric(
        horizontal: AppConstants.mdPadding,
        vertical: AppConstants.smPadding,
      ),
    );
  }
  
  // Card styles
  static BoxDecoration getCardDecoration({
    Color? backgroundColor,
    double? borderRadius,
    double? elevation,
  }) {
    return BoxDecoration(
      color: backgroundColor ?? AppColors.surface,
      borderRadius: BorderRadius.circular(
        borderRadius ?? AppConstants.mdRadius,
      ),
      boxShadow: [
        BoxShadow(
          color: AppColors.shadow,
          spreadRadius: 0,
          blurRadius: elevation ?? AppConstants.smElevation,
          offset: const Offset(0, 2),
        ),
      ],
    );
  }
  
  // Gradient styles
  static LinearGradient getPrimaryGradient({
    AlignmentGeometry? begin,
    AlignmentGeometry? end,
  }) {
    return LinearGradient(
      begin: begin ?? Alignment.topCenter,
      end: end ?? Alignment.bottomCenter,
      colors: [AppColors.primary, AppColors.primaryDark],
    );
  }
}
