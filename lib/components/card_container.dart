import 'package:flutter/material.dart';

class CardContainer extends StatelessWidget {
  const CardContainer({
    super.key,
    required this.child,
    this.padding = const EdgeInsets.all(24),
    this.margin,
    this.borderRadius = 30.0,
    this.backgroundColor = Colors.white,
    this.elevation = 20.0,
    this.shadowColor,
    this.shadowOffset,
    this.border,
  });

  final Widget child;
  final EdgeInsetsGeometry padding;
  final EdgeInsetsGeometry? margin;
  final double borderRadius;
  final Color backgroundColor;
  final double elevation;
  final Color? shadowColor;
  final Offset? shadowOffset;
  final BoxBorder? border;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: margin,
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.only(
          topLeft: Radius.circular(borderRadius),
          topRight: Radius.circular(borderRadius),
        ),
        boxShadow: [
          BoxShadow(
            color: shadowColor ?? Colors.black.withOpacity(0.1),
            blurRadius: elevation,
            offset: shadowOffset ?? const Offset(0, -5),
          ),
        ],
        border: border,
      ),
      child: Padding(padding: padding, child: child),
    );
  }
}
