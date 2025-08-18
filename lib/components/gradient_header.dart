import 'package:flutter/material.dart';
import 'custom_text.dart';
import '../core/colors.dart';

class GradientHeader extends StatelessWidget {
  const GradientHeader({
    super.key,
    required this.title,
    required this.subtitle,
    this.logo,
    this.height,
    this.gradientColors,
    this.languageToggle,
    this.isArabic = false,
  });

  final String title;
  final String subtitle;
  final Widget? logo;
  final double? height;
  final List<Color>? gradientColors;
  final Widget? languageToggle;
  final bool isArabic;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: height ?? MediaQuery.of(context).size.height * 0.3,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors:
              gradientColors ?? [AppColors.primary, const Color(0xFF00695C)],
        ),
      ),
      child: Stack(
        children: [
          // Background overlay
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(color: Colors.black.withOpacity(0.1)),
            ),
          ),
          // Language toggle
          if (languageToggle != null)
            Positioned(
              top: MediaQuery.of(context).padding.top + 20,
              right: isArabic ? 20 : null,
              left: isArabic ? null : 20,
              child: languageToggle!,
            ),
          // Logo and text
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                if (logo != null) ...[logo!, const SizedBox(height: 20)],
                CustomText(
                  text: title,
                  size: 28,
                  weight: FontWeight.bold,
                  color: Colors.white,
                ),
                const SizedBox(height: 15),
                CustomText(
                  text: subtitle,
                  size: 18,
                  color: Colors.white70,
                  weight: FontWeight.w800,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
