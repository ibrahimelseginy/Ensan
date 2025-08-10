import 'package:ensan_test/Home/screens/splash.dart';
import 'package:ensan_test/auth/forget_password.dart';
import 'package:ensan_test/auth/login_screen.dart';
import 'package:flutter/material.dart';

void main() {
  runApp(Ensan());
}

class Ensan extends StatelessWidget {
  const Ensan({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Ensan',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      debugShowCheckedModeBanner: false,
      routes: {
        '/': (context) => const Splash(),
        LoginScreen.routeName: (context) => const LoginScreen(),
        ForgetPassword.routeName: (context) => const ForgetPassword(),
      },
      initialRoute: '/',
    );
  }
}
