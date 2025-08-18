import 'package:ensan_test/router/app_router.dart';
import 'package:flutter/material.dart';
import 'package:gap/gap.dart';
import 'custom_text.dart';
import 'custom_text_field.dart';
import 'custom_button.dart';

class LoginForm extends StatelessWidget {
  const LoginForm({
    super.key,
    required this.emailController,
    required this.passwordController,
    required this.onEmailChanged,
    required this.onPasswordChanged,
    required this.onPasswordVisibilityChanged,
    required this.onLoginSubmitted,
    required this.onForgotPassword,
    required this.onRegister,
    required this.onGuestLogin,
    required this.isArabic,
    required this.isPasswordVisible,
    required this.isValid,
    required this.isLoading,
    this.emailError,
    this.passwordError,
    this.errorMessage,
  });

  final TextEditingController emailController;
  final TextEditingController passwordController;
  final Function(String) onEmailChanged;
  final Function(String) onPasswordChanged;
  final VoidCallback onPasswordVisibilityChanged;
  final VoidCallback onLoginSubmitted;
  final VoidCallback onForgotPassword;
  final VoidCallback onRegister;
  final VoidCallback onGuestLogin;
  final bool isArabic;
  final bool isPasswordVisible;
  final bool isValid;
  final bool isLoading;
  final String? emailError;
  final String? passwordError;
  final String? errorMessage;

  @override
  Widget build(BuildContext context) {
    return Form(
      key: GlobalKey<FormState>(), // Add this line
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Email field
          CustomTextField(
            label: isArabic ? "البريد الالكتروني" : "Email",
            controller: emailController,
            onChanged: onEmailChanged,
            keyboardType: TextInputType.emailAddress,
            prefixIcon: Icon(Icons.email, color: Colors.grey[600]),
            errorText: emailError,
            textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
            textAlign: isArabic ? TextAlign.right : TextAlign.left,
          ),
          Gap(20),

          // Password field
          CustomTextField(
            label: isArabic ? "الباسورد" : "Password",
            controller: passwordController,
            onChanged: onPasswordChanged,
            isPassword: true,
            isPasswordVisible: isPasswordVisible,
            onPasswordVisibilityChanged: onPasswordVisibilityChanged,
            prefixIcon: Icon(Icons.lock, color: Colors.grey[600]),
            errorText: passwordError,
            textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
            textAlign: isArabic ? TextAlign.right : TextAlign.left,
          ),
          Gap(20),

          // Forgot password
          Align(
            alignment: Alignment.centerRight,
            child: TextButton(
              onPressed: onForgotPassword,
              child: CustomText(
                text: isArabic ? "نسيت كلمة المرور؟ " : "Forgot Password?",
                size: 16,
                color: const Color(0xFF00695C),
                weight: FontWeight.w800,
              ),
            ),
          ),
          Gap(20),

          // Error message
          if (errorMessage != null) ...[
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red[50],
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.red[200]!),
              ),
              child: Text(
                errorMessage!,
                style: TextStyle(color: Colors.red[700], fontSize: 14),
                textAlign: TextAlign.center,
              ),
            ),
            Gap(20),
          ],

          // Login button
          CustomButton(
            
            title: isArabic ? "تسجيل الدخول" : "Login",
            onTap: onLoginSubmitted,
            backgroundColor: const Color(0xFF00695C),
            textColor: Colors.white,
            height: 56,
            isLoading: isLoading,
          ),
          Gap(20),

          // Register link
          Center(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: isArabic
                  ? [
                      TextButton(
                        onPressed: onRegister,
                        child: CustomText(
                          text: "تسجيل حساب",
                          size: 16,
                          color: const Color(0xFF00695C),
                          weight: FontWeight.w800,
                        ),
                      ),
                      CustomText(
                        text: "ليس لديك حساب ؟ ",
                        size: 16,
                        color: Colors.black,
                        weight: FontWeight.w800,
                      ),
                    ]
                  : [
                      CustomText(
                        text: "Don't have an account? ",
                        size: 16,
                        color: Colors.black,
                        weight: FontWeight.w800,
                      ),
                      TextButton(
                        onPressed: onRegister,
                        child: CustomText(
                          text: "Register",
                          size: 16,
                          color: const Color(0xFF00695C),
                          weight: FontWeight.w800,
                        ),
                      ),
                    ],
            ),
          ),
          Gap(20),

          // Guest login button
          CustomButton(
            title: isArabic ? "الدخول كزائر" : "Login as Guest",
            onTap: onGuestLogin,
            backgroundColor: const Color(0xFF00695C).withOpacity(0.8),
            textColor: Colors.white,
            height: 56,
          ),
        ],
      ),
    );
  }
}
