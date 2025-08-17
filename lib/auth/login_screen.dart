import 'package:ensan_test/auth/forget_password.dart';
import 'package:ensan_test/auth/cubit/login_cubit.dart';
import 'package:ensan_test/router/app_router.dart';
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
  late LoginCubit _loginCubit;

  @override
  void initState() {
    super.initState();
    _loginCubit = LoginCubit();
    _loginCubit.initialize();
  }

  @override
  void dispose() {
    _loginCubit.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: ListenableBuilder(
        listenable: _loginCubit,
        builder: (context, child) {
          final state = _loginCubit.state;

          return Column(
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
                      right: state.isArabic ? 20 : null,
                      left: state.isArabic ? null : 20,
                      child: GestureDetector(
                        onTap: () {
                          _loginCubit.onLanguageChanged(!state.isArabic);
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
                              text: state.isArabic ? "en" : "ع",
                              size: 18,
                              weight: FontWeight.bold,
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
                                      color: const Color.fromARGB(
                                        255,
                                        10,
                                        80,
                                        45,
                                      ),
                                      size: 40,
                                    ),
                                    Gap(5),
                                    // Text part
                                    Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        CustomText(
                                          text: state.isArabic
                                              ? "إنسان"
                                              : "Ensan",
                                          size: 30,
                                          weight: FontWeight.bold,
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                                Gap(5),
                                CustomText(
                                  text: state.isArabic
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
                            text: state.isArabic ? "مرحبا بك" : "Welcome ",
                            size: 28,
                            weight: FontWeight.bold,
                            color: Colors.white,
                          ),
                          Gap(15),
                          CustomText(
                            text: state.isArabic
                                ? "الرجاء تسجيل الدخول"
                                : "Please log in",
                            size: 18,
                            color: Colors.white,
                            weight: FontWeight.w800,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              // Login form card
              Container(
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
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Email/Phone input
                        Align(
                          alignment: state.isArabic
                              ? Alignment.centerRight
                              : Alignment.centerLeft,
                          child: TextFormField(
                            controller: TextEditingController(
                              text: state.email,
                            ),
                            onChanged: _loginCubit.onEmailChanged,
                            textDirection: state.isArabic
                                ? TextDirection.ltr
                                : TextDirection.rtl,
                            textAlign: state.isArabic
                                ? TextAlign.right
                                : TextAlign.left,
                            decoration: InputDecoration(
                              hintText: state.isArabic
                                  ? "البريد الالكتروني"
                                  : "Email",
                              hintStyle: TextStyle(
                                color: Colors.grey[400],
                                fontSize: 16,
                              ),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide(
                                  color: Colors.grey[300]!,
                                ),
                              ),
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide(
                                  color: Colors.grey[300]!,
                                ),
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
                              suffixIcon: state.isArabic
                                  ? Icon(Icons.email, color: Colors.grey[600])
                                  : null,
                              prefixIcon: state.isArabic
                                  ? null
                                  : Icon(Icons.email, color: Colors.grey[600]),
                              errorText: state.emailError,
                            ),
                          ),
                        ),
                        Gap(20),
                        // Password input
                        TextFormField(
                          controller: TextEditingController(
                            text: state.password,
                          ),
                          onChanged: _loginCubit.onPasswordChanged,
                          obscureText: !state.isPasswordVisible,
                          textDirection: state.isArabic
                              ? TextDirection.rtl
                              : TextDirection.ltr,
                          textAlign: state.isArabic
                              ? TextAlign.right
                              : TextAlign.left,
                          decoration: InputDecoration(
                            hintText: state.isArabic ? "الباسورد" : "Password",
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
                            suffixIcon: state.isArabic
                                ? IconButton(
                                    icon: Icon(
                                      state.isPasswordVisible
                                          ? Icons.visibility
                                          : Icons.visibility_off,
                                      color: Colors.grey[600],
                                    ),
                                    onPressed:
                                        _loginCubit.onPasswordVisibilityChanged,
                                  )
                                : null,
                            prefixIcon: state.isArabic
                                ? Icon(Icons.lock, color: Colors.grey[600])
                                : IconButton(
                                    icon: Icon(
                                      state.isPasswordVisible
                                          ? Icons.visibility
                                          : Icons.visibility_off,
                                      color: Colors.grey[600],
                                    ),
                                    onPressed:
                                        _loginCubit.onPasswordVisibilityChanged,
                                  ),
                            errorText: state.passwordError,
                          ),
                        ),
                        Gap(20),
                        // Forgot password
                        Align(
                          alignment: Alignment.centerRight,
                          child: TextButton(
                            onPressed: () =>
                                AppRouter.navigateToForgetPassword(context),

                            child: CustomText(
                              text: state.isArabic
                                  ? "نسيت كلمة المرور؟ "
                                  : "Forgot Password?",
                              size: 16,
                              color: Color(0xFF00695C),
                              weight: FontWeight.w800,
                            ),
                          ),
                        ),
                        Gap(20),
                        // Error message
                        if (state.errorMessage != null)
                          Container(
                            width: double.infinity,
                            padding: EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.red[50],
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(color: Colors.red[200]!),
                            ),
                            child: Text(
                              state.errorMessage!,
                              style: TextStyle(
                                color: Colors.red[700],
                                fontSize: 14,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ),
                        if (state.errorMessage != null) Gap(20),
                        // Login button
                        SizedBox(
                          width: double.infinity,
                          height: 56,
                          child: ElevatedButton(
                            onPressed: state.isValid && !state.isLoading
                                ? _loginCubit.onLoginSubmitted
                                : null,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Color(0xFF00695C),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                              elevation: 0,
                            ),
                            child: state.isLoading
                                ? SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(
                                      color: Colors.white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : CustomText(
                                    text: state.isArabic
                                        ? "تسجيل الدخول"
                                        : "Login",
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
                                  AppRouter.navigateToRegister(context);
                                },
                                child: CustomText(
                                  text: state.isArabic
                                      ? "تسجيل حساب"
                                      : "Register",
                                  size: 16,
                                  color: Color(0xFF00695C),
                                  weight: FontWeight.w800,
                                ),
                              ),
                              CustomText(
                                text: state.isArabic
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
                              backgroundColor: Color(
                                0xFF00695C,
                              ).withOpacity(0.8),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                              elevation: 0,
                            ),
                            child: CustomText(
                              text: state.isArabic
                                  ? "الدخول كزائر"
                                  : "Login as Guest",
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
            ],
          );
        },
      ),
    );
  }
}
