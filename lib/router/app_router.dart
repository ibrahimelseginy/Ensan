import 'package:ensan_test/auth/forget_password.dart';
import 'package:flutter/material.dart';
import '../auth/login_screen.dart';
import '../Home/screens/splash.dart';
import '../auth/register_screen.dart';

class AppRouter {
  static const String splash = '/';
  static const String login = '/login';
  static const String home = '/home';
  static const String forgetPassword = '/forget-password';
  static const String register = '/register';

  static Map<String, WidgetBuilder> get routes => {
    splash: (context) => const Splash(),
    login: (context) => const LoginScreen(),
    home: (context) => const SizedBox(), // Placeholder for home screen
    forgetPassword: (context) => const ForgetPassword(),
    '/register': (context) => const RegisterScreen(),
  };

  static void navigateToLogin(BuildContext context) {
    Navigator.pushReplacementNamed(context, login);
  }

  static void navigateToSplash(BuildContext context) {
    Navigator.pushReplacementNamed(context, splash);
  }

  static void navigateToHome(BuildContext context) {
    // Implement navigation to home screen if needed
    // Navigator.pushReplacementNamed(context, home);
  }
  static void navigateToForgetPassword(BuildContext context) {
    Navigator.pushNamed(context, forgetPassword);
  }

  static void navigateToRegister(BuildContext context) {
    Navigator.pushNamed(context, register);
  }
}
