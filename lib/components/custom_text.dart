import 'package:flutter/material.dart';

class CustomText extends StatelessWidget {
  const CustomText({
    super.key,
    required this.text,
    this.size = 16,
    this.weight = FontWeight.normal,
    this.color = Colors.white,
    this.maxLines,
    this.height = 1.0,
    this.letterSpacing = 1.0,
    this.textAlign = TextAlign.start,
  });

  final String text;
  final double size;
  final FontWeight weight;
  final Color color;
  final int? maxLines;
  final double height;
  final double letterSpacing;
  final TextAlign textAlign;

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      maxLines: maxLines,
      textAlign: textAlign,
      style: TextStyle(
        letterSpacing: letterSpacing,
        fontSize: size,
        color: color,
        fontWeight: weight,
        height: height,
        fontFamily: "TenorSans",
      ),
    );
  }
}
