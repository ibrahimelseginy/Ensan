import 'package:flutter/material.dart';
import '../core/colors.dart';
import '../core/common_styles.dart';
import '../core/constants/app_constants.dart';

class CommonWidgets {
  // Service Icon Widget
  static Widget serviceIcon({
    required IconData icon,
    required String label,
    required Color color,
    double size = 60,
    double iconSize = 30,
    double labelSize = 12,
  }) {
    return Column(
      children: [
        Container(
          width: size,
          height: size,
          decoration: BoxDecoration(
            color: AppColors.white,
            shape: BoxShape.circle,
            boxShadow: CommonStyles.smShadow,
          ),
          child: Icon(icon, color: color, size: iconSize),
        ),
        const SizedBox(height: AppConstants.smPadding),
        Text(
          label,
          style: TextStyle(
            fontSize: labelSize,
            color: AppColors.textPrimary,
          ),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }

  // Tag Widget
  static Widget tag({
    required String text,
    Color? backgroundColor,
    Color? textColor,
    double? borderRadius,
    EdgeInsets? padding,
  }) {
    return Container(
      padding: padding ?? const EdgeInsets.symmetric(
        horizontal: AppConstants.mdPadding,
        vertical: AppConstants.smPadding,
      ),
      decoration: BoxDecoration(
        color: backgroundColor ?? AppColors.success,
        borderRadius: BorderRadius.circular(
          borderRadius ?? AppConstants.xlRadius,
        ),
      ),
      child: Text(
        text,
        style: TextStyle(
          color: textColor ?? AppColors.white,
          fontSize: AppConstants.mdText,
          fontWeight: FontWeight.w500,
        ),
      ),
    );
  }

  // Navigation Item Widget
  static Widget navigationItem({
    required IconData icon,
    required String label,
    required bool isActive,
    Color? activeColor,
    Color? inactiveColor,
    double iconSize = 24,
    double labelSize = 12,
  }) {
    final color = isActive 
        ? (activeColor ?? AppColors.primary) 
        : (inactiveColor ?? AppColors.grey);
    
    return Column(
      children: [
        Icon(icon, color: color, size: iconSize),
        const SizedBox(height: AppConstants.xsPadding),
        Text(
          label,
          style: TextStyle(
            fontSize: labelSize,
            color: color,
          ),
        ),
      ],
    );
  }

  // Card Container Widget
  static Widget cardContainer({
    required Widget child,
    EdgeInsets? padding,
    EdgeInsets? margin,
    BoxDecoration? decoration,
    double? borderRadius,
    List<BoxShadow>? boxShadow,
  }) {
    return Container(
      margin: margin ?? CommonStyles.mdMargin,
      padding: padding ?? CommonStyles.mdPadding,
      decoration: decoration ?? BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(
          borderRadius ?? AppConstants.mdRadius,
        ),
        boxShadow: boxShadow ?? CommonStyles.smShadow,
      ),
      child: child,
    );
  }

  // Gradient Container Widget
  static Widget gradientContainer({
    required Widget child,
    EdgeInsets? padding,
    EdgeInsets? margin,
    List<Color>? colors,
    AlignmentGeometry? begin,
    AlignmentGeometry? end,
    double? borderRadius,
  }) {
    return Container(
      margin: margin ?? CommonStyles.mdMargin,
      padding: padding ?? CommonStyles.mdPadding,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: begin ?? Alignment.topCenter,
          end: end ?? Alignment.bottomCenter,
          colors: colors ?? [AppColors.primary, AppColors.primaryDark],
        ),
        borderRadius: BorderRadius.circular(
          borderRadius ?? AppConstants.mdRadius,
        ),
      ),
      child: child,
    );
  }

  // Horizontal Scrollable Row Widget
  static Widget horizontalScrollableRow({
    required List<Widget> children,
    EdgeInsets? padding,
    ScrollPhysics? physics,
    bool reverse = false,
    TextDirection? textDirection,
  }) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: physics,
      reverse: reverse,
      child: Container(
        padding: padding ?? const EdgeInsets.symmetric(
          vertical: AppConstants.lgPadding,
        ),
        child: Directionality(
          textDirection: textDirection ?? TextDirection.rtl,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: children,
          ),
        ),
      ),
    );
  }

  // Section Header Widget
  static Widget sectionHeader({
    required String title,
    TextStyle? titleStyle,
    Widget? trailing,
    EdgeInsets? padding,
  }) {
    return Padding(
      padding: padding ?? CommonStyles.mdPadding,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            title,
            style: titleStyle ?? CommonStyles.titleText,
          ),
          if (trailing != null) trailing,
        ],
      ),
    );
  }

  // Loading Widget
  static Widget loading({
    Color? color,
    double? size,
  }) {
    return Center(
      child: SizedBox(
        width: size ?? AppConstants.xlIcon,
        height: size ?? AppConstants.xlIcon,
        child: CircularProgressIndicator(
          color: color ?? AppColors.primary,
          strokeWidth: 3,
        ),
      ),
    );
  }

  // Empty State Widget
  static Widget emptyState({
    required String message,
    IconData? icon,
    Color? iconColor,
    double? iconSize,
  }) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          if (icon != null) ...[
            Icon(
              icon,
              color: iconColor ?? AppColors.grey,
              size: iconSize ?? AppConstants.xxlIcon,
            ),
            const SizedBox(height: AppConstants.mdPadding),
          ],
          Text(
            message,
            style: CommonStyles.captionText,
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}
