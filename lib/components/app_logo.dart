import 'package:flutter/material.dart';
import 'package:gap/gap.dart';
import 'custom_text.dart';

class AppLogo extends StatelessWidget {
  const AppLogo({
    super.key,
    required this.isArabic,
    this.iconSize = 40,
    this.textSize = 30,
    this.subtitleSize = 16,
    this.iconColor,
    this.subtitleColor,
  });

  final bool isArabic;
  final double iconSize;
  final double textSize;
  final double subtitleSize;
  final Color? iconColor;
  final Color? subtitleColor;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(
                Icons.favorite,
                color: iconColor ?? const Color.fromARGB(255, 10, 80, 45),
                size: iconSize,
              ),
              Gap(5),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomText(
                    text: isArabic ? "إنسان" : "Ensan",
                    size: textSize,
                    weight: FontWeight.bold,
                  ),
                ],
              ),
            ],
          ),
          Gap(5),
          CustomText(
            text: isArabic
                ? "نبنـــــي جيـــــل...يبنــــي حيــــاة"
                : "We build a generation...that builds life",
            size: subtitleSize,
            color: subtitleColor ?? Colors.white70,
          ),
        ],
      ),
    );
  }
}
