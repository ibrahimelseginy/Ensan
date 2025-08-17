import 'package:flutter/foundation.dart';

// State class
class LoginState {
  final String email;
  final String password;
  final bool isPasswordVisible;
  final bool isArabic;
  final bool isValid;
  final String? emailError;
  final String? passwordError;
  final bool isLoading;
  final String? errorMessage;

  const LoginState({
    this.email = '',
    this.password = '',
    this.isPasswordVisible = false,
    this.isArabic = true,
    this.isValid = false,
    this.emailError,
    this.passwordError,
    this.isLoading = false,
    this.errorMessage,
  });

  LoginState copyWith({
    String? email,
    String? password,
    bool? isPasswordVisible,
    bool? isArabic,
    bool? isValid,
    String? emailError,
    String? passwordError,
    bool? isLoading,
    String? errorMessage,
  }) {
    return LoginState(
      email: email ?? this.email,
      password: password ?? this.password,
      isPasswordVisible: isPasswordVisible ?? this.isPasswordVisible,
      isArabic: isArabic ?? this.isArabic,
      isValid: isValid ?? this.isValid,
      emailError: emailError ?? this.emailError,
      passwordError: passwordError ?? this.passwordError,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage ?? this.errorMessage,
    );
  }
}

// Cubit using ChangeNotifier
class LoginCubit extends ChangeNotifier {
  LoginState _state = const LoginState();

  LoginState get state => _state;

  void onEmailChanged(String email) {
    final emailError = _validateEmail(email);
    final isValid = _validateForm(email, _state.password);

    _state = _state.copyWith(
      email: email,
      emailError: emailError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onPasswordChanged(String password) {
    final passwordError = _validatePassword(password);
    final isValid = _validateForm(_state.email, password);

    _state = _state.copyWith(
      password: password,
      passwordError: passwordError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onPasswordVisibilityChanged() {
    _state = _state.copyWith(isPasswordVisible: !_state.isPasswordVisible);
    notifyListeners();
  }

  void onLanguageChanged(bool isArabic) {
    _state = _state.copyWith(isArabic: isArabic);
    notifyListeners();
  }

  Future<void> onLoginSubmitted() async {
    if (_state.isValid) {
      _state = _state.copyWith(isLoading: true, errorMessage: null);
      notifyListeners();

      try {
        // Simulate API call
        await Future.delayed(const Duration(seconds: 2));

        // Here you would typically make your actual login API call
        // For now, we'll just emit success
        _state = _state.copyWith(isLoading: false);
        notifyListeners();

        // You can add navigation logic here
        // Navigator.of(context).pushReplacementNamed('/home');
      } catch (e) {
        _state = _state.copyWith(isLoading: false, errorMessage: e.toString());
        notifyListeners();
      }
    }
  }

  void reset() {
    _state = const LoginState();
    notifyListeners();
  }

  void initialize() {
    _state = const LoginState();
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

  String? _validatePassword(String password) {
    if (password.isEmpty) {
      return _state.isArabic
          ? 'يرجى إدخال كلمة المرور'
          : 'Please enter your password';
    }
    if (password.length < 6) {
      return _state.isArabic
          ? 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
          : 'Password must be at least 6 characters';
    }
    return null;
  }

  bool _validateForm(String email, String password) {
    return _validateEmail(email) == null && _validatePassword(password) == null;
  }
}
