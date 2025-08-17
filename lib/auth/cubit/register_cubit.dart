import 'package:flutter/foundation.dart';

// State class
class RegisterState {
  final String fullName;
  final String email;
  final String phone;
  final String password;
  final String confirmPassword;
  final bool isPasswordVisible;
  final bool isConfirmPasswordVisible;
  final bool isArabic;
  final bool isValid;
  final String? fullNameError;
  final String? emailError;
  final String? phoneError;
  final String? passwordError;
  final String? confirmPasswordError;
  final bool isLoading;
  final String? errorMessage;
  final bool isSuccess;

  const RegisterState({
    this.fullName = '',
    this.email = '',
    this.phone = '',
    this.password = '',
    this.confirmPassword = '',
    this.isPasswordVisible = false,
    this.isConfirmPasswordVisible = false,
    this.isArabic = true,
    this.isValid = false,
    this.fullNameError,
    this.emailError,
    this.phoneError,
    this.passwordError,
    this.confirmPasswordError,
    this.isLoading = false,
    this.errorMessage,
    this.isSuccess = false,
  });

  RegisterState copyWith({
    String? fullName,
    String? email,
    String? phone,
    String? password,
    String? confirmPassword,
    bool? isPasswordVisible,
    bool? isConfirmPasswordVisible,
    bool? isArabic,
    bool? isValid,
    String? fullNameError,
    String? emailError,
    String? phoneError,
    String? passwordError,
    String? confirmPasswordError,
    bool? isLoading,
    String? errorMessage,
    bool? isSuccess,
  }) {
    return RegisterState(
      fullName: fullName ?? this.fullName,
      email: email ?? this.email,
      phone: phone ?? this.phone,
      password: password ?? this.password,
      confirmPassword: confirmPassword ?? this.confirmPassword,
      isPasswordVisible: isPasswordVisible ?? this.isPasswordVisible,
      isConfirmPasswordVisible:
          isConfirmPasswordVisible ?? this.isConfirmPasswordVisible,
      isArabic: isArabic ?? this.isArabic,
      isValid: isValid ?? this.isValid,
      fullNameError: fullNameError ?? this.fullNameError,
      emailError: emailError ?? this.emailError,
      phoneError: phoneError ?? this.phoneError,
      passwordError: passwordError ?? this.passwordError,
      confirmPasswordError: confirmPasswordError ?? this.confirmPasswordError,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage ?? this.errorMessage,
      isSuccess: isSuccess ?? this.isSuccess,
    );
  }
}

// Cubit using ChangeNotifier
class RegisterCubit extends ChangeNotifier {
  RegisterState _state = const RegisterState();

  RegisterState get state => _state;

  void onFullNameChanged(String fullName) {
    final fullNameError = _validateFullName(fullName);
    final isValid = _validateForm();

    _state = _state.copyWith(
      fullName: fullName,
      fullNameError: fullNameError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onEmailChanged(String email) {
    final emailError = _validateEmail(email);
    final isValid = _validateForm();

    _state = _state.copyWith(
      email: email,
      emailError: emailError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onPhoneChanged(String phone) {
    final phoneError = _validatePhone(phone);
    final isValid = _validateForm();

    _state = _state.copyWith(
      phone: phone,
      phoneError: phoneError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onPasswordChanged(String password) {
    final passwordError = _validatePassword(password);
    final confirmPasswordError = _validateConfirmPassword(
      _state.confirmPassword,
      password,
    );
    final isValid = _validateForm();

    _state = _state.copyWith(
      password: password,
      passwordError: passwordError,
      confirmPasswordError: confirmPasswordError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onConfirmPasswordChanged(String confirmPassword) {
    final confirmPasswordError = _validateConfirmPassword(
      confirmPassword,
      _state.password,
    );
    final isValid = _validateForm();

    _state = _state.copyWith(
      confirmPassword: confirmPassword,
      confirmPasswordError: confirmPasswordError,
      isValid: isValid,
    );
    notifyListeners();
  }

  void onPasswordVisibilityChanged() {
    _state = _state.copyWith(isPasswordVisible: !_state.isPasswordVisible);
    notifyListeners();
  }

  void onConfirmPasswordVisibilityChanged() {
    _state = _state.copyWith(
      isConfirmPasswordVisible: !_state.isConfirmPasswordVisible,
    );
    notifyListeners();
  }

  void onLanguageChanged(bool isArabic) {
    _state = _state.copyWith(isArabic: isArabic);
    notifyListeners();
  }

  Future<void> onRegisterSubmitted() async {
    if (_state.isValid) {
      _state = _state.copyWith(isLoading: true, errorMessage: null);
      notifyListeners();

      try {
        // Simulate API call
        await Future.delayed(const Duration(seconds: 2));

        // Here you would typically make your actual register API call
        // For now, we'll just emit success
        _state = _state.copyWith(isLoading: false, isSuccess: true);
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
    _state = const RegisterState();
    notifyListeners();
  }

  void initialize() {
    _state = const RegisterState();
    notifyListeners();
  }

  // Validation methods
  String? _validateFullName(String fullName) {
    if (fullName.isEmpty) {
      return _state.isArabic
          ? 'يرجى إدخال الاسم الكامل'
          : 'Please enter your full name';
    }
    if (fullName.length < 3) {
      return _state.isArabic
          ? 'الاسم يجب أن يكون 3 أحرف على الأقل'
          : 'Name must be at least 3 characters';
    }
    return null;
  }

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

  String? _validatePhone(String phone) {
    if (phone.isEmpty) {
      return _state.isArabic
          ? 'يرجى إدخال رقم الهاتف'
          : 'Please enter your phone number';
    }
    if (phone.length < 10) {
      return _state.isArabic
          ? 'رقم الهاتف يجب أن يكون 10 أرقام على الأقل'
          : 'Phone number must be at least 10 digits';
    }
    return null;
  }

  String? _validatePassword(String password) {
    if (password.isEmpty) {
      return _state.isArabic
          ? 'يرجى إدخال كلمة المرور'
          : 'Please enter your password';
    }
    if (password.length < 8) {
      return _state.isArabic
          ? 'كلمة المرور يجب أن تكون 8 أحرف على الأقل'
          : 'Password must be at least 8 characters';
    }
    if (!RegExp(r'^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)').hasMatch(password)) {
      return _state.isArabic
          ? 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم'
          : 'Password must contain uppercase, lowercase and number';
    }
    return null;
  }

  String? _validateConfirmPassword(String confirmPassword, String password) {
    if (confirmPassword.isEmpty) {
      return _state.isArabic
          ? 'يرجى تأكيد كلمة المرور'
          : 'Please confirm your password';
    }
    if (confirmPassword != password) {
      return _state.isArabic
          ? 'كلمة المرور غير متطابقة'
          : 'Passwords do not match';
    }
    return null;
  }

  bool _validateForm() {
    return _validateFullName(_state.fullName) == null &&
        _validateEmail(_state.email) == null &&
        _validatePhone(_state.phone) == null &&
        _validatePassword(_state.password) == null &&
        _validateConfirmPassword(_state.confirmPassword, _state.password) ==
            null;
  }
}
