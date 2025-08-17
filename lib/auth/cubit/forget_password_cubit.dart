import 'package:flutter/foundation.dart';

// State class for forget password
class ForgetPasswordState {
  final String email;
  final bool isEmailValid;
  final String? emailError;
  final bool isLoading;
  final bool isEmailSent;
  final String? errorMessage;
  final String? successMessage;
  final bool isArabic;

  const ForgetPasswordState({
    this.email = '',
    this.isEmailValid = false,
    this.emailError,
    this.isLoading = false,
    this.isEmailSent = false,
    this.errorMessage,
    this.successMessage,
    this.isArabic = true,
  });

  ForgetPasswordState copyWith({
    String? email,
    bool? isEmailValid,
    String? emailError,
    bool? isLoading,
    bool? isEmailSent,
    String? errorMessage,
    String? successMessage,
    bool? isArabic,
  }) {
    return ForgetPasswordState(
      email: email ?? this.email,
      isEmailValid: isEmailValid ?? this.isEmailValid,
      emailError: emailError ?? this.emailError,
      isLoading: isLoading ?? this.isLoading,
      isEmailSent: isEmailSent ?? this.isEmailSent,
      errorMessage: errorMessage ?? this.errorMessage,
      successMessage: successMessage ?? this.successMessage,
      isArabic: isArabic ?? this.isArabic,
    );
  }
}

// Cubit for forget password
class ForgetPasswordCubit extends ChangeNotifier {
  ForgetPasswordState _state = const ForgetPasswordState();

  ForgetPasswordState get state => _state;

  void onEmailChanged(String email) {
    final emailError = _validateEmail(email);
    final isEmailValid = emailError == null;

    _state = _state.copyWith(
      email: email,
      emailError: emailError,
      isEmailValid: isEmailValid,
      errorMessage: null,
      successMessage: null,
    );
    notifyListeners();
  }

  void onLanguageChanged(bool isArabic) {
    _state = _state.copyWith(isArabic: isArabic);
    notifyListeners();
  }

  Future<void> onResetPasswordSubmitted() async {
    if (_state.isEmailValid) {
      _state = _state.copyWith(
        isLoading: true,
        errorMessage: null,
        successMessage: null,
      );
      notifyListeners();

      try {
        // Simulate API call for password reset
        await Future.delayed(const Duration(seconds: 2));

        // Here you would typically make your actual password reset API call
        // For now, we'll just simulate success

        _state = _state.copyWith(
          isLoading: false,
          isEmailSent: true,
          successMessage: _state.isArabic
              ? 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني'
              : 'Password reset link has been sent to your email',
        );
        notifyListeners();
      } catch (e) {
        _state = _state.copyWith(
          isLoading: false,
          errorMessage: _state.isArabic
              ? 'حدث خطأ أثناء إرسال رابط إعادة تعيين كلمة المرور'
              : 'An error occurred while sending the password reset link',
        );
        notifyListeners();
      }
    }
  }

  void reset() {
    _state = const ForgetPasswordState();
    notifyListeners();
  }

  void clearMessages() {
    _state = _state.copyWith(errorMessage: null, successMessage: null);
    notifyListeners();
  }

  // Validation methods
  String? _validateEmail(String email) {
    if (email.isEmpty) {
      return _state.isArabic
          ? 'يرجى إدخال البريد الإلكتروني'
          : 'Please enter your email';
    }
    if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(email)) {
      return _state.isArabic
          ? 'يرجى إدخال بريد إلكتروني صحيح'
          : 'Please enter a valid email';
    }
    return null;
  }
}
