import 'package:ensan_test/Home/screens/home_screen.dart';
import 'package:ensan_test/auth/forget_password.dart';
import 'package:flutter/material.dart';
import '../auth/login_screen.dart';
import '../Home/screens/splash.dart';
import '../auth/register_screen.dart';

class AppRouter {
  static const String splash = '/';
  static const String login = '/login';
  static const String homeScreen = '/home_screen';
  static const String forgetPassword = '/forget-password';
  static const String register = '/register';

  static Map<String, WidgetBuilder> get routes => {
    splash: (context) => const Splash(),
    login: (context) => const LoginScreen(),
    forgetPassword: (context) => const ForgetPassword(),
    register: (context) => const RegisterScreen(),
    homeScreen: (context) => const HomeScreen(),
  };

  static void navigateToLogin(BuildContext context) {
    Navigator.pushReplacementNamed(context, login);
  }

  static void navigateToSplash(BuildContext context) {
    Navigator.pushReplacementNamed(context, splash);
  }

  static void navigateToHomeScreen(BuildContext context) {
    // Implement navigation to home screen if needed
    Navigator.pushReplacementNamed(context, homeScreen);
  }

  static void navigateToForgetPassword(BuildContext context) {
    Navigator.pushNamed(context, forgetPassword);
  }

  static void navigateToRegister(BuildContext context) {
    Navigator.pushNamed(context, register);
  }
}
