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
    this.width,
    this.borderRadius = 12.0,
    this.elevation = 0,
    this.disabled = false,
    this.textSize = 18,
    this.fontWeight = FontWeight.w600,
    this.padding,
    this.margin,
    this.border,
    this.gradient,
  });

  final String title;
  final VoidCallback onTap;
  final bool isLoading;
  final Widget? icon;
  final Color? backgroundColor;
  final Color? textColor;
  final double height;
  final double? width;
  final double borderRadius;
  final double elevation;
  final bool disabled;
  final double textSize;
  final FontWeight fontWeight;
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final BoxBorder? border;
  final Gradient? gradient;

  @override
  Widget build(BuildContext context) {
    final isDisabled = disabled || isLoading;

    return Container(
      width: width,
      height: height,
      margin: margin,
      decoration: BoxDecoration(
        color: gradient == null ? (backgroundColor ?? Color(0xFF00695C)) : null,
        gradient: gradient,
        borderRadius: BorderRadius.circular(borderRadius),
        border: border,
        boxShadow: elevation > 0
            ? [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: elevation,
                  offset: const Offset(0, 2),
                ),
              ]
            : null,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: isDisabled ? null : onTap,
          borderRadius: BorderRadius.circular(borderRadius),
          child: Container(
            padding: padding ?? const EdgeInsets.symmetric(horizontal: 16),
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
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        if (icon != null) ...[icon!, const Gap(10)],
                        Flexible(
                          child: CustomText(
                            text: title,
                            size: textSize,
                            color: textColor ?? Colors.white,
                            weight: fontWeight,
                            textAlign: TextAlign.center,
                          ),
                        ),
                      ],
                    ),
            ),
          ),
        ),
      ),
    );
  }
}
