import 'package:ensan_test/auth/login_screen.dart';
import 'package:flutter/material.dart';

class Splash extends StatefulWidget {
  const Splash({super.key});

  @override
  State<Splash> createState() => _SplashState();
}

class _SplashState extends State<Splash> {
  @override
  Widget build(BuildContext context) {
    Future.delayed(
      Duration(seconds: 2),
      () => Navigator.push(
        // ignore: use_build_context_synchronously
        context,
        MaterialPageRoute(
          builder: (c) {
            return LoginScreen();
          },
        ),
      ),
    );

    return Scaffold(
      backgroundColor: Colors.black,
      body: Center(child: Image.asset("assets/logo/ensan.jpg", width: 150)),
    );
  }
}
