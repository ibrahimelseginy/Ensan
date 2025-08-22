import 'package:flutter/material.dart';
import 'package:gap/gap.dart';
import '../core/colors.dart';
import '../core/constants/app_constants.dart';

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
    this.width,
    this.borderRadius,
    this.elevation,
    this.disabled = false,
    this.textSize,
    this.fontWeight,
    this.padding,
    this.margin,
    this.border,
    this.gradient,
    this.buttonType = ButtonType.primary,
  });

  final String title;
  final VoidCallback onTap;
  final bool isLoading;
  final Widget? icon;
  final Color? backgroundColor;
  final Color? textColor;
  final double height;
  final double? width;
  final double? borderRadius;
  final double? elevation;
  final bool disabled;
  final double? textSize;
  final FontWeight? fontWeight;
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final BoxBorder? border;
  final Gradient? gradient;
  final ButtonType buttonType;

  @override
  Widget build(BuildContext context) {
    final isDisabled = disabled || isLoading;
    final effectiveBorderRadius = borderRadius ?? AppConstants.mdRadius;
    final effectiveElevation = elevation ?? AppConstants.xsElevation;
    final effectiveTextSize = textSize ?? AppConstants.mdText;
    final effectiveFontWeight = fontWeight ?? FontWeight.w600;

    return Container(
      width: width,
      height: height,
      margin: margin,
      decoration: BoxDecoration(
        color: _getBackgroundColor(),
        gradient: gradient ?? _getGradient(),
        borderRadius: BorderRadius.circular(effectiveBorderRadius),
        border: border ?? _getBorder(),
        boxShadow: effectiveElevation > 0 ? _getShadow(effectiveElevation) : null,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: isDisabled ? null : onTap,
          borderRadius: BorderRadius.circular(effectiveBorderRadius),
          child: Container(
            padding: padding ?? _getDefaultPadding(),
            child: Center(
              child: isLoading
                  ? _buildLoadingIndicator()
                  : _buildButtonContent(effectiveTextSize, effectiveFontWeight),
            ),
          ),
        ),
      ),
    );
  }

  Color _getBackgroundColor() {
    if (gradient != null) return Colors.transparent;
    
    switch (buttonType) {
      case ButtonType.primary:
        return backgroundColor ?? AppColors.primary;
      case ButtonType.secondary:
        return backgroundColor ?? AppColors.secondary;
      case ButtonType.outlined:
        return Colors.transparent;
      case ButtonType.text:
        return Colors.transparent;
    }
  }

  Gradient? _getGradient() {
    if (gradient != null) return gradient;
    
    switch (buttonType) {
      case ButtonType.primary:
        return LinearGradient(
          colors: [AppColors.primary, AppColors.primaryDark],
        );
      default:
        return null;
    }
  }

  BoxBorder? _getBorder() {
    if (border != null) return border;
    
    switch (buttonType) {
      case ButtonType.outlined:
        return Border.all(
          color: backgroundColor ?? AppColors.primary,
          width: 1.5,
        );
      default:
        return null;
    }
  }

  List<BoxShadow> _getShadow(double elevation) {
    return [
      BoxShadow(
        color: AppColors.shadow,
        blurRadius: elevation,
        offset: const Offset(0, 2),
      ),
    ];
  }

  EdgeInsetsGeometry _getDefaultPadding() {
    return const EdgeInsets.symmetric(
      horizontal: AppConstants.mdPadding,
      vertical: AppConstants.smPadding,
    );
  }

  Widget _buildLoadingIndicator() {
    return SizedBox(
      width: AppConstants.mdIcon,
      height: AppConstants.mdIcon,
      child: CircularProgressIndicator(
        strokeWidth: 2,
        valueColor: AlwaysStoppedAnimation<Color>(
          _getTextColor(),
        ),
      ),
    );
  }

  Widget _buildButtonContent(double textSize, FontWeight fontWeight) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      mainAxisSize: MainAxisSize.min,
      children: [
        if (icon != null) ...[
          icon!,
          const Gap(AppConstants.smPadding),
        ],
        Flexible(
          child: Text(
            title,
            style: TextStyle(
              fontSize: textSize,
              color: _getTextColor(),
              fontWeight: fontWeight,
            ),
            textAlign: TextAlign.center,
          ),
        ),
      ],
    );
  }

  Color _getTextColor() {
    if (textColor != null) return textColor!;
    
    switch (buttonType) {
      case ButtonType.primary:
      case ButtonType.secondary:
        return AppColors.white;
      case ButtonType.outlined:
      case ButtonType.text:
        return backgroundColor ?? AppColors.primary;
    }
  }
}

enum ButtonType {
  primary,
  secondary,
  outlined,
  text,
}
