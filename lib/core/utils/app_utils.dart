import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class AppUtils {
  // Number Formatting
  static String formatNumber(num number) {
    return NumberFormat('#,###').format(number);
  }

  static String formatCurrency(num amount, {String currency = 'ج.م'}) {
    return '${formatNumber(amount)} $currency';
  }

  // Date Formatting
  static String formatDate(DateTime date, {String format = 'dd/MM/yyyy'}) {
    return DateFormat(format).format(date);
  }

  static String formatRelativeDate(DateTime date) {
    final now = DateTime.now();
    final difference = now.difference(date);

    if (difference.inDays > 0) {
      return 'منذ ${difference.inDays} يوم';
    } else if (difference.inHours > 0) {
      return 'منذ ${difference.inHours} ساعة';
    } else if (difference.inMinutes > 0) {
      return 'منذ ${difference.inMinutes} دقيقة';
    } else {
      return 'الآن';
    }
  }

  // Validation Helpers
  static bool isValidEmail(String email) {
    return RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(email);
  }

  static bool isValidPhone(String phone) {
    // Egyptian phone number validation
    return RegExp(r'^(\+20|0)?1[0125][0-9]{8}$').hasMatch(phone);
  }

  static bool isValidPassword(String password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$').hasMatch(password);
  }

  // String Helpers
  static String capitalizeFirst(String text) {
    if (text.isEmpty) return text;
    return text[0].toUpperCase() + text.substring(1).toLowerCase();
  }

  static String truncateText(String text, int maxLength) {
    if (text.length <= maxLength) return text;
    return '${text.substring(0, maxLength)}...';
  }

  // Color Helpers
  static Color darkenColor(Color color, double amount) {
    assert(amount >= 0 && amount <= 1);
    final hsl = HSLColor.fromColor(color);
    final hslDark = hsl.withLightness((hsl.lightness - amount).clamp(0.0, 1.0));
    return hslDark.toColor();
  }

  static Color lightenColor(Color color, double amount) {
    assert(amount >= 0 && amount <= 1);
    final hsl = HSLColor.fromColor(color);
    final hslLight = hsl.withLightness((hsl.lightness + amount).clamp(0.0, 1.0));
    return hslLight.toColor();
  }

  // Responsive Helpers
  static bool isMobile(BuildContext context) {
    return MediaQuery.of(context).size.width < 600;
  }

  static bool isTablet(BuildContext context) {
    return MediaQuery.of(context).size.width >= 600 && 
           MediaQuery.of(context).size.width < 1200;
  }

  static bool isDesktop(BuildContext context) {
    return MediaQuery.of(context).size.width >= 1200;
  }

  // Animation Helpers
  static Duration getAnimationDuration(AnimationSpeed speed) {
    switch (speed) {
      case AnimationSpeed.fast:
        return const Duration(milliseconds: 200);
      case AnimationSpeed.normal:
        return const Duration(milliseconds: 300);
      case AnimationSpeed.slow:
        return const Duration(milliseconds: 500);
    }
  }

  static Curve getAnimationCurve(AnimationCurveType curveType) {
    switch (curveType) {
      case AnimationCurveType.easeIn:
        return Curves.easeIn;
      case AnimationCurveType.easeOut:
        return Curves.easeOut;
      case AnimationCurveType.easeInOut:
        return Curves.easeInOut;
      case AnimationCurveType.bounce:
        return Curves.bounceOut;
    }
  }
}

enum AnimationSpeed { fast, normal, slow }
enum AnimationCurveType { easeIn, easeOut, easeInOut, bounce }




