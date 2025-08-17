import 'package:ensan_test/auth/cubit/register_cubit.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../components/custom_text_field.dart';
import '../components/custom_button.dart';
import '../components/custom_text.dart';
import '../core/colors.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _fullNameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<RegisterCubit>().initialize();
    });
  }

  @override
  void dispose() {
    _fullNameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  // Helper method for translations
  String _getText(String arabicText, String englishText, RegisterCubit cubit) {
    return cubit.state.isArabic ? arabicText : englishText;
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<RegisterCubit>(
      builder: (context, cubit, child) {
        return Directionality(
          textDirection: cubit.state.isArabic
              ? TextDirection.rtl
              : TextDirection.ltr,
          child: Scaffold(
            backgroundColor: Colors.white,
            body: SafeArea(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24.0),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Header Section
                      _buildHeader(),
                      const SizedBox(height: 40),

                      // Form Fields
                      _buildFormFields(cubit),
                      const SizedBox(height: 32),

                      // Register Button
                      _buildRegisterButton(cubit),
                      const SizedBox(height: 24),

                      // Login Link
                      _buildLoginLink(),
                      const SizedBox(height: 24),

                      // Language Toggle
                      _buildLanguageToggle(cubit),
                    ],
                  ),
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildHeader() {
    return Consumer<RegisterCubit>(
      builder: (context, cubit, child) {
        return Column(
          children: [
            // Logo
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: const Color(0xFF00695C).withOpacity(0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Icon(
                Icons.person_add_rounded,
                size: 40,
                color: const Color(0xFF00695C),
              ),
            ),
            const SizedBox(height: 24),

            // Title
            CustomText(
              text: _getText('إنشاء حساب جديد', 'Create New Account', cubit),
              size: 32, // تكبير من 28 إلى 32
              weight: FontWeight.bold,
              color: const Color(0xFF00695C),
            ),
            const SizedBox(height: 8),

            // Subtitle
            CustomText(
              text: _getText(
                'أدخل بياناتك لإنشاء حساب جديد',
                'Enter your details to create a new account',
                cubit,
              ),
              size: 18, // تكبير من 16 إلى 18
              color: Colors.grey,
            ),
          ],
        );
      },
    );
  }

  Widget _buildFormFields(RegisterCubit cubit) {
    return Column(
      children: [
        // Full Name Field
        CustomTextField(
          label: _getText('الاسم الكامل', 'Full Name', cubit),
          controller: _fullNameController,
          onChanged: cubit.onFullNameChanged,
          textDirection: cubit.state.isArabic
              ? TextDirection.rtl
              : TextDirection.ltr,
        ),
        if (cubit.state.fullNameError != null)
          Padding(
            padding: EdgeInsets.only(
              top: 8.0,
              left: cubit.state.isArabic ? 12.0 : 0.0,
              right: cubit.state.isArabic ? 0.0 : 12.0,
            ),
            child: Row(
              children: [
                Icon(Icons.error_outline, color: Colors.red, size: 16),
                const SizedBox(width: 4),
                Expanded(
                  child: CustomText(
                    text: cubit.state.fullNameError!,
                    size: 14, // تكبير من 12 إلى 14
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ),
        const SizedBox(height: 20),

        // Email Field
        CustomTextField(
          label: _getText('البريد الإلكتروني', 'Email Address', cubit),
          controller: _emailController,
          onChanged: cubit.onEmailChanged,
          keyboardType: TextInputType.emailAddress,
          textDirection: TextDirection.ltr,
        ),
        if (cubit.state.emailError != null)
          Padding(
            padding: EdgeInsets.only(
              top: 8.0,
              left: cubit.state.isArabic ? 12.0 : 0.0,
              right: cubit.state.isArabic ? 0.0 : 12.0,
            ),
            child: Row(
              children: [
                Icon(Icons.error_outline, color: Colors.red, size: 16),
                const SizedBox(width: 4),
                Expanded(
                  child: CustomText(
                    text: cubit.state.emailError!,
                    size: 14, // تكبير من 12 إلى 14
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ),
        const SizedBox(height: 20),

        // Phone Field
        CustomTextField(
          label: _getText('رقم الهاتف', 'Phone Number', cubit),
          controller: _phoneController,
          onChanged: cubit.onPhoneChanged,
          keyboardType: TextInputType.phone,
          textDirection: TextDirection.ltr,
        ),
        if (cubit.state.phoneError != null)
          Padding(
            padding: EdgeInsets.only(
              top: 8.0,
              left: cubit.state.isArabic ? 12.0 : 0.0,
              right: cubit.state.isArabic ? 0.0 : 12.0,
            ),
            child: Row(
              children: [
                Icon(Icons.error_outline, color: Colors.red, size: 16),
                const SizedBox(width: 4),
                Expanded(
                  child: CustomText(
                    text: cubit.state.phoneError!,
                    size: 14, // تكبير من 12 إلى 14
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ),
        const SizedBox(height: 20),

        // Password Field
        CustomTextField(
          label: _getText('كلمة المرور', 'Password', cubit),
          controller: _passwordController,
          onChanged: cubit.onPasswordChanged,
          isPassword: true,
          isPasswordVisible: cubit.state.isPasswordVisible,
          onPasswordVisibilityChanged: cubit.onPasswordVisibilityChanged,
          textDirection: TextDirection.ltr,
        ),
        if (cubit.state.passwordError != null)
          Padding(
            padding: EdgeInsets.only(
              top: 8.0,
              left: cubit.state.isArabic ? 12.0 : 0.0,
              right: cubit.state.isArabic ? 0.0 : 12.0,
            ),
            child: Row(
              children: [
                Icon(Icons.error_outline, color: Colors.red, size: 16),
                const SizedBox(width: 4),
                Expanded(
                  child: CustomText(
                    text: cubit.state.passwordError!,
                    size: 14, // تكبير من 12 إلى 14
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ),
        const SizedBox(height: 20),

        // Confirm Password Field
        CustomTextField(
          label: _getText('تأكيد كلمة المرور', 'Confirm Password', cubit),
          controller: _confirmPasswordController,
          onChanged: cubit.onConfirmPasswordChanged,
          isPassword: true,
          isPasswordVisible: cubit.state.isConfirmPasswordVisible,
          onPasswordVisibilityChanged: cubit.onConfirmPasswordVisibilityChanged,
          textDirection: TextDirection.ltr,
        ),
        if (cubit.state.confirmPasswordError != null)
          Padding(
            padding: EdgeInsets.only(
              top: 8.0,
              left: cubit.state.isArabic ? 12.0 : 0.0,
              right: cubit.state.isArabic ? 0.0 : 12.0,
            ),
            child: Row(
              children: [
                Icon(Icons.error_outline, color: Colors.red, size: 16),
                const SizedBox(width: 4),
                Expanded(
                  child: CustomText(
                    text: cubit.state.confirmPasswordError!,
                    size: 14, // تكبير من 12 إلى 14
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ),
      ],
    );
  }

  Widget _buildRegisterButton(RegisterCubit cubit) {
    return CustomButton(
      title: cubit.state.isLoading
          ? _getText('جاري التسجيل...', 'Creating Account...', cubit)
          : _getText('إنشاء الحساب', 'Create Account', cubit),
      onTap: () => cubit.state.isValid && !cubit.state.isLoading
          ? cubit.onRegisterSubmitted
          : null,
      backgroundColor: const Color(0xFF00695C),
      textColor: Colors.white,
      height: 56,
    );
  }

  Widget _buildLoginLink() {
    return Consumer<RegisterCubit>(
      builder: (context, cubit, child) {
        return Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CustomText(
              text: _getText(
                'لديك حساب بالفعل؟',
                'Already have an account?',
                cubit,
              ),
              size: 16, // تكبير من 14 إلى 16
              color: Colors.grey,
            ),
            TextButton(
              onPressed: () {
                Navigator.pop(context);
              },
              child: CustomText(
                text: _getText('تسجيل الدخول', 'Sign In', cubit),
                size: 16, // تكبير من 14 إلى 16
                weight: FontWeight.bold,
                color: const Color(0xFF00695C),
              ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildLanguageToggle(RegisterCubit cubit) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const CustomText(
          text: 'English',
          size: 16,
          color: Colors.grey,
        ), // تكبير من 14 إلى 16
        Switch(
          value: cubit.state.isArabic,
          onChanged: cubit.onLanguageChanged,
          activeColor: const Color(0xFF00695C),
        ),
        const CustomText(
          text: 'العربية',
          size: 16,
          color: Colors.grey,
        ), // تكبير من 14 إلى 16
      ],
    );
  }
}
