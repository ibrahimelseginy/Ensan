import 'package:ensan_test/auth/forget_password.dart';
import 'package:ensan_test/components/custom_appbar.dart';
import 'package:flutter/material.dart';
import 'package:gap/gap.dart';
import '../components/custom_text.dart';

import '../core/colors.dart';

class LoginScreen extends StatefulWidget {
  static const String routeName = '/login';
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final GlobalKey<FormState> formKey = GlobalKey<FormState>();
  bool isPasswordVisible = false;
  bool isArabic = true; // true for Arabic, false for English
  bool rememberMe = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          // Background with children image (simulated)
          Container(
            height: MediaQuery.of(context).size.height * 0.3,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  AppColors.primary, // Teal color
                  Color(0xFF00695C),
                ],
              ),
            ),
            child: Stack(
              children: [
                // Simulated background image (you can replace with actual image)
                Positioned.fill(
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.black.withOpacity(0.1),
                    ),
                  ),
                ),
                // Language switch button
                Positioned(
                  top: MediaQuery.of(context).padding.top + 20,
                  right: 20,
                  child: GestureDetector(
                    onTap: () {
                      setState(() {
                        isArabic = !isArabic;
                      });
                    },
                    child: Container(
                      width: 42,
                      height: 35,
                      decoration: BoxDecoration(
                        color: AppColors.primary,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Center(
                        child: CustomText(
                          text: isArabic ? "en" : "ع",
                          size: 18,
                          weight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ),
                ),
                // Logo and welcome text
                Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // Logo container - New Ensan logo
                      Container(
                        padding: EdgeInsets.all(20),
                        child: Column(
                          children: [
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(
                                  Icons.favorite,
                                  color: const Color.fromARGB(255, 10, 80, 45),
                                  size: 40,
                                ),
                                Gap(5),
                                // Text part
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    CustomText(
                                      text: isArabic ? "إنسان" : "Ensan",
                                      size: 30,
                                      weight: FontWeight.bold,
                                      color: Colors.white,
                                    ),
                                  ],
                                ),

                                // Teal/light blue color
                              ],
                            ),
                            Gap(5),
                            CustomText(
                              text:
                                  isArabic
                                      ? "نبنـــــي جيـــــل...يبنــــي حيــــاة"
                                      : "We build a generation...that builds life",
                              size: 16,
                              color: Colors.white70,
                            ),
                          ],
                        ),
                      ),
                      Gap(5),
                      // Welcome messages
                      CustomText(
                        text: isArabic ? "مرحبا بك" : "Welcome ",
                        size: 28,
                        weight: FontWeight.bold,
                        color: Colors.white,
                      ),
                      Gap(15),
                      CustomText(
                        text:
                            isArabic ? "الرجاء تسجيل الدخول" : "Please log in",
                        size: 18,
                        color: Colors.white,
                        weight: FontWeight.bold,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          // Login form card
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            child: Container(
              height: MediaQuery.of(context).size.height * 0.7,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(30),
                  topRight: Radius.circular(30),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    blurRadius: 20,
                    offset: Offset(0, -5),
                  ),
                ],
              ),
              child: Padding(
                padding: EdgeInsets.all(24),
                child: Form(
                  key: formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Email/Phone input
                      Align(
                        alignment:
                            isArabic
                                ? Alignment.centerRight
                                : Alignment.centerLeft,
                        child: TextFormField(
                          controller: emailController,
                          decoration: InputDecoration(
                            hintText: isArabic ? "البريد الالكتروني" : "Email",

                            hintStyle: TextStyle(
                              color: Colors.grey[400],
                              fontSize: 16,
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide(color: Colors.grey[300]!),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide(color: Colors.grey[300]!),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                              borderSide: BorderSide(
                                color: Color(0xFF00897B),
                                width: 2,
                              ),
                            ),
                            contentPadding: EdgeInsets.symmetric(
                              horizontal: 16,
                              vertical: 16,
                            ),

                            suffixIcon: Icon(
                              Icons.email,
                              color: Colors.grey[600],
                            ),
                            prefixIcon: Text(
                              "@",
                              style: TextStyle(
                                color: Colors.grey[600],
                                fontSize: 28,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return isArabic
                                  ? 'يرجى إدخال البريد الإلكتروني'
                                  : 'Please enter your email';
                            }
                            return null;
                          },
                        ),
                      ),
                      Gap(20),
                      TextFormField(
                        controller: passwordController,
                        obscureText: !isPasswordVisible,
                        decoration: InputDecoration(
                          hintText: isArabic ? "الباسورد" : "Password",
                          hintStyle: TextStyle(
                            color: Colors.grey[400],
                            fontSize: 16,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.grey[300]!),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.grey[300]!),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(
                              color: Color(0xFF00897B),
                              width: 2,
                            ),
                          ),
                          contentPadding: EdgeInsets.symmetric(
                            horizontal: 16,
                            vertical: 16,
                          ),
                          suffixIcon: IconButton(
                            icon: Icon(
                              isPasswordVisible
                                  ? Icons.visibility
                                  : Icons.visibility_off,
                              color: Colors.grey[600],
                            ),
                            onPressed: () {
                              setState(() {
                                isPasswordVisible = !isPasswordVisible;
                              });
                            },
                          ),
                          prefixIcon: Icon(Icons.lock, color: Colors.grey[600]),
                        ),
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return isArabic
                                ? 'يرجى إدخال كلمة المرور'
                                : 'Please enter your password';
                          }
                          return null;
                        },
                      ),
                      Gap(10),
                      // Remember me checkbox
                      Row(
                        children: [
                          GestureDetector(
                            onTap: () {
                              setState(() {
                                rememberMe = !rememberMe;
                              });
                            },
                            child: Container(
                              width: 20,
                              height: 20,
                              decoration: BoxDecoration(
                                color:
                                    rememberMe
                                        ? Color(0xFF00695C)
                                        : Colors.transparent,
                                border: Border.all(
                                  color:
                                      rememberMe
                                          ? Color(0xFF00695C)
                                          : Colors.grey[400]!,
                                  width: 2,
                                ),
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child:
                                  rememberMe
                                      ? Icon(
                                        Icons.check,
                                        color: Colors.white,
                                        size: 14,
                                      )
                                      : null,
                            ),
                          ),
                          Gap(12),
                          CustomText(
                            text: isArabic ? "تذكرني" : "Remember me",
                            size: 16,
                            color: Colors.black,
                            weight: FontWeight.w800,
                          ),
                        ],
                      ),
                      Gap(10),
                      // Forgot password
                      Align(
                        alignment: Alignment.centerRight,
                        child: TextButton(
                          onPressed: () {
                            // Navigate to forgot password screen
                          },
                          child: InkWell(
                            onTap: () {
                              Navigator.of(
                                context,
                              ).pushNamed(ForgetPassword.routeName);
                            },
                            child: CustomText(
                              text:
                                  isArabic
                                      ? "نسيت كلمة المرور؟ "
                                      : "Forgot Password?",
                              size: 16,
                              color: Color(0xFF00695C),
                              weight: FontWeight.w800,
                            ),
                          ),
                        ),
                      ),
                      Gap(20),
                      // Login button
                      SizedBox(
                        width: double.infinity,
                        height: 56,
                        child: ElevatedButton(
                          onPressed: _login,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Color(0xFF00695C),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                            elevation: 0,
                          ),
                          child: CustomText(
                            text: isArabic ? "تسجيل الدخول" : "Login",
                            size: 18,
                            weight: FontWeight.w600,
                            color: Colors.white,
                          ),
                        ),
                      ),
                      Gap(20),

                      // No account link
                      Center(
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            TextButton(
                              onPressed: () {
                                // Navigate to register screen
                              },
                              child: CustomText(
                                text: isArabic ? "تسجيل حساب" : "Register",
                                size: 16,
                                color: Color(0xFF00695C),
                                weight: FontWeight.w800,
                              ),
                            ),
                            CustomText(
                              text:
                                  isArabic
                                      ? "ليس لديك حساب ؟ "
                                      : "Don't have an account? ",
                              size: 16,
                              color: Colors.black,
                              weight: FontWeight.w800,
                            ),
                          ],
                        ),
                      ),
                      Gap(20),
                      // Guest login button
                      SizedBox(
                        width: double.infinity,
                        height: 56,
                        child: ElevatedButton(
                          onPressed: () {
                            // Navigate to home as guest
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Color(0xFF00695C).withOpacity(0.8),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                            elevation: 0,
                          ),
                          child: CustomText(
                            text: isArabic ? "الدخول كزائر" : "Login as Guest",
                            size: 18,
                            weight: FontWeight.w600,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _login() {
    if (formKey.currentState!.validate()) {
      // Implement login logic here
      print('Email: ${emailController.text}');
      print('Password: ${passwordController.text}');
      print('Remember me: $rememberMe');

      // Navigate to home screen or show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            isArabic ? 'تم تسجيل الدخول بنجاح!' : 'Login successful!',
          ),
          backgroundColor: Colors.green,
        ),
      );
    }
  }

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }
}
