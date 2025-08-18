import 'package:ensan_test/auth/cubit/login_cubit.dart';
import 'package:ensan_test/router/app_router.dart';
import 'package:flutter/material.dart';
import '../components/gradient_header.dart';
import '../components/app_logo.dart';
import '../components/language_toggle.dart';
import '../components/card_container.dart';
import '../components/login_form.dart';

class LoginScreen extends StatefulWidget {
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
              // Header with gradient background
              GradientHeader(
                title: state.isArabic ? "مرحبا بك" : "Welcome",
                subtitle: state.isArabic
                    ? "الرجاء تسجيل الدخول"
                    : "Please log in",
                logo: AppLogo(isArabic: state.isArabic),
                languageToggle: LanguageToggle(
                  isArabic: state.isArabic,
                  onLanguageChanged: _loginCubit.onLanguageChanged,
                ),
              ),

              // Login form card
              Expanded(
                child: CardContainer(
                  child: LoginForm(
                    emailController: TextEditingController(text: state.email),
                    passwordController: TextEditingController(
                      text: state.password,
                    ),
                    onEmailChanged: _loginCubit.onEmailChanged,
                    onPasswordChanged: _loginCubit.onPasswordChanged,
                    onPasswordVisibilityChanged:
                        _loginCubit.onPasswordVisibilityChanged,
                    onLoginSubmitted: _loginCubit.onLoginSubmitted,
                    onForgotPassword: () =>
                        AppRouter.navigateToForgetPassword(context),
                    onRegister: () => AppRouter.navigateToRegister(context),
                    onGuestLogin: () => AppRouter.navigateToHomeScreen(context),
                    isArabic: state.isArabic,
                    isPasswordVisible: state.isPasswordVisible,
                    isValid: state.isValid,
                    isLoading: state.isLoading,
                    emailError: state.emailError,
                    passwordError: state.passwordError,
                    errorMessage: state.errorMessage,
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
