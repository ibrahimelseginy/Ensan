import 'package:flutter/material.dart';
import 'colors.dart';
import 'constants/app_constants.dart';

class CommonStyles {
  // Common spacing
  static const EdgeInsets xsPadding = EdgeInsets.all(AppConstants.xsPadding);
  static const EdgeInsets smPadding = EdgeInsets.all(AppConstants.smPadding);
  static const EdgeInsets mdPadding = EdgeInsets.all(AppConstants.mdPadding);
  static const EdgeInsets lgPadding = EdgeInsets.all(AppConstants.lgPadding);
  static const EdgeInsets xlPadding = EdgeInsets.all(AppConstants.xlPadding);
  
  // Horizontal padding
  static const EdgeInsets xsHPadding = EdgeInsets.symmetric(horizontal: AppConstants.xsPadding);
  static const EdgeInsets smHPadding = EdgeInsets.symmetric(horizontal: AppConstants.smPadding);
  static const EdgeInsets mdHPadding = EdgeInsets.symmetric(horizontal: AppConstants.mdPadding);
  static const EdgeInsets lgHPadding = EdgeInsets.symmetric(horizontal: AppConstants.lgPadding);
  static const EdgeInsets xlHPadding = EdgeInsets.symmetric(horizontal: AppConstants.xlPadding);
  
  // Vertical padding
  static const EdgeInsets xsVPadding = EdgeInsets.symmetric(vertical: AppConstants.xsPadding);
  static const EdgeInsets smVPadding = EdgeInsets.symmetric(vertical: AppConstants.smPadding);
  static const EdgeInsets mdVPadding = EdgeInsets.symmetric(vertical: AppConstants.mdPadding);
  static const EdgeInsets lgVPadding = EdgeInsets.symmetric(vertical: AppConstants.lgPadding);
  static const EdgeInsets xlVPadding = EdgeInsets.symmetric(vertical: AppConstants.xlPadding);
  
  // Common margins
  static const EdgeInsets xsMargin = EdgeInsets.all(AppConstants.xsPadding);
  static const EdgeInsets smMargin = EdgeInsets.all(AppConstants.smPadding);
  static const EdgeInsets mdMargin = EdgeInsets.all(AppConstants.mdPadding);
  static const EdgeInsets lgMargin = EdgeInsets.all(AppConstants.lgPadding);
  static const EdgeInsets xlMargin = EdgeInsets.all(AppConstants.xlPadding);
  
  // Common border radius
  static const BorderRadius xsRadius = BorderRadius.all(Radius.circular(AppConstants.xsRadius));
  static const BorderRadius smRadius = BorderRadius.all(Radius.circular(AppConstants.smRadius));
  static const BorderRadius mdRadius = BorderRadius.all(Radius.circular(AppConstants.mdRadius));
  static const BorderRadius lgRadius = BorderRadius.all(Radius.circular(AppConstants.lgRadius));
  static const BorderRadius xlRadius = BorderRadius.all(Radius.circular(AppConstants.xlRadius));
  static const BorderRadius xxlRadius = BorderRadius.all(Radius.circular(AppConstants.xxlRadius));
  
  // Common shadows
  static List<BoxShadow> get xsShadow => [
    BoxShadow(
      color: AppColors.shadowLight,
      spreadRadius: 0,
      blurRadius: AppConstants.xsElevation,
      offset: const Offset(0, 1),
    ),
  ];
  
  static List<BoxShadow> get smShadow => [
    BoxShadow(
      color: AppColors.shadow,
      spreadRadius: 0,
      blurRadius: AppConstants.smElevation,
      offset: const Offset(0, 2),
    ),
  ];
  
  static List<BoxShadow> get mdShadow => [
    BoxShadow(
      color: AppColors.shadow,
      spreadRadius: 0,
      blurRadius: AppConstants.mdElevation,
      offset: const Offset(0, 4),
    ),
  ];
  
  static List<BoxShadow> get lgShadow => [
    BoxShadow(
      color: AppColors.shadow,
      spreadRadius: 0,
      blurRadius: AppConstants.lgElevation,
      offset: const Offset(0, 8),
    ),
  ];
  
  // Common decorations
  static BoxDecoration get cardDecoration => BoxDecoration(
    color: AppColors.surface,
    borderRadius: mdRadius,
    boxShadow: smShadow,
  );
  
  static BoxDecoration get elevatedCardDecoration => BoxDecoration(
    color: AppColors.surface,
    borderRadius: mdRadius,
    boxShadow: mdShadow,
  );
  
  static BoxDecoration get primaryGradientDecoration => BoxDecoration(
    gradient: LinearGradient(
      begin: Alignment.topCenter,
      end: Alignment.bottomCenter,
      colors: [AppColors.primary, AppColors.primaryDark],
    ),
  );
  
  // Common text styles
  static const TextStyle titleText = TextStyle(
    fontSize: AppConstants.titleText,
    fontWeight: FontWeight.bold,
    color: AppColors.textPrimary,
  );
  
  static const TextStyle subtitleText = TextStyle(
    fontSize: AppConstants.xlText,
    fontWeight: FontWeight.w600,
    color: AppColors.textSecondary,
  );
  
  static const TextStyle bodyText = TextStyle(
    fontSize: AppConstants.mdText,
    fontWeight: FontWeight.normal,
    color: AppColors.textPrimary,
  );
  
  static const TextStyle captionText = TextStyle(
    fontSize: AppConstants.smText,
    fontWeight: FontWeight.normal,
    color: AppColors.textSecondary,
  );
  
  static const TextStyle buttonText = TextStyle(
    fontSize: AppConstants.mdText,
    fontWeight: FontWeight.w600,
    color: AppColors.white,
  );
}
