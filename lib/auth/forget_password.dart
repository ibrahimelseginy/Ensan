import 'package:flutter/material.dart';

class ForgetPassword extends StatelessWidget {
  static const String routeName = '/forget-password';
  const ForgetPassword({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: Text(
          'Forget Password Screen',
          style: TextStyle(fontSize: 24, color: Colors.black),
        ),
      ),
    );
  }
}
