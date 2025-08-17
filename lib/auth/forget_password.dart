import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'cubit/forget_password_cubit.dart';
import '../components/custom_button.dart';
import '../components/custom_text_field.dart';
import '../components/custom_text.dart';
import '../core/colors.dart';

class ForgetPassword extends StatefulWidget {
  const ForgetPassword({super.key});

  @override
  State<ForgetPassword> createState() => _ForgetPasswordState();
}

class _ForgetPasswordState extends State<ForgetPassword> {
  late ForgetPasswordCubit _cubit;
  final TextEditingController _emailController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _cubit = ForgetPasswordCubit();
  }

  @override
  void dispose() {
    _emailController.dispose();
    _cubit.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider.value(
      value: _cubit,
      child: Consumer<ForgetPasswordCubit>(
        builder: (context, cubit, child) {
          return Scaffold(
            backgroundColor: Colors.white,
            appBar: AppBar(
              backgroundColor: Colors.transparent,
              elevation: 0,
              leading: IconButton(
                icon: Icon(Icons.arrow_back_ios, color: Color(0xFF00695C)),
                onPressed: () => Navigator.of(context).pop(),
              ),
              actions: [
                IconButton(
                  icon: Icon(
                    cubit.state.isArabic ? Icons.language : Icons.translate,
                    color: Color(0xFF00695C),
                  ),
                  onPressed: () {
                    cubit.onLanguageChanged(!cubit.state.isArabic);
                  },
                ),
              ],
            ),
            body: Padding(
              padding: const EdgeInsets.all(24.0),
              child: Directionality(
                textDirection: cubit.state.isArabic
                    ? TextDirection.rtl
                    : TextDirection.ltr,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 20),

                    // Header
                    CustomText(
                      text: cubit.state.isArabic
                          ? 'نسيت كلمة المرور؟'
                          : 'Forgot Password?',
                      size: 28,
                      weight: FontWeight.w900,
                      color: Colors.black87,
                    ),

                    const SizedBox(height: 12),

                    CustomText(
                      text: cubit.state.isArabic
                          ? 'أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور'
                          : 'Enter your email and we\'ll send you a link to reset your password',
                      size: 16,
                      weight: FontWeight.w700,
                      color: Colors.grey[600]!,
                      maxLines: 3,
                    ),

                    const SizedBox(height: 40),

                    // Email Field
                    CustomTextField(
                      controller: _emailController,
                      label: cubit.state.isArabic
                          ? 'البريد الإلكتروني'
                          : 'Email',
                      keyboardType: TextInputType.emailAddress,
                      onChanged: cubit.onEmailChanged,
                      textDirection: cubit.state.isArabic
                          ? TextDirection.rtl
                          : TextDirection.ltr,
                    ),

                    const SizedBox(height: 32),

                    // Reset Password Button
                    SizedBox(
                      width: double.infinity,
                      child: CustomButton(
                        title: cubit.state.isLoading
                            ? (cubit.state.isArabic
                                  ? 'جاري الإرسال...'
                                  : 'Sending...')
                            : (cubit.state.isArabic
                                  ? 'إرسال رابط إعادة التعيين'
                                  : 'Send Reset Link'),
                        onTap:
                            cubit.state.isEmailValid && !cubit.state.isLoading
                            ? cubit.onResetPasswordSubmitted
                            : () {},
                        isLoading: cubit.state.isLoading,
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Error Message
                    if (cubit.state.errorMessage != null)
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.red[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.red[200]!),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.error_outline, color: Colors.red[600]),
                            const SizedBox(width: 12),
                            Expanded(
                              child: CustomText(
                                text: cubit.state.errorMessage!,
                                color: Colors.red[700]!,
                                size: 14,
                                weight: FontWeight.w700,
                              ),
                            ),
                            IconButton(
                              icon: Icon(Icons.close, color: Colors.red[600]),
                              onPressed: cubit.clearMessages,
                              iconSize: 20,
                            ),
                          ],
                        ),
                      ),

                    // Success Message
                    if (cubit.state.successMessage != null)
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.green[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Color(0xFF00695C)),
                        ),
                        child: Row(
                          children: [
                            Icon(
                              Icons.check_circle_outline,
                              color: Color(0xFF00695C),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: CustomText(
                                text: cubit.state.successMessage!,
                                color: Color(0xFF00695C),
                                size: 14,
                                weight: FontWeight.w700,
                              ),
                            ),
                            IconButton(
                              icon: Icon(Icons.close, color: Color(0xFF00695C)),
                              onPressed: cubit.clearMessages,
                              iconSize: 20,
                            ),
                          ],
                        ),
                      ),

                    const Spacer(),

                    // Back to Login
                    Center(
                      child: TextButton(
                        onPressed: () => Navigator.of(context).pop(),
                        child: CustomText(
                          text: cubit.state.isArabic
                              ? 'العودة إلى تسجيل الدخول'
                              : 'Back to Login',
                          color: Color(0xFF00695C),
                          size: 16,
                          weight: FontWeight.w800,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}
