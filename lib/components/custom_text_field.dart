import 'package:flutter/material.dart';

class CustomTextField extends StatelessWidget {
  const CustomTextField({
    super.key,
    required this.label,
    required this.controller,
    this.onChanged,
    this.keyboardType = TextInputType.text,
    this.isPassword = false,
    this.isPasswordVisible = false,
    this.onPasswordVisibilityChanged,
    this.textDirection,
  });

  final String label;
  final TextEditingController controller;
  final Function(String)? onChanged;
  final TextInputType keyboardType;
  final bool isPassword;
  final bool isPasswordVisible;
  final VoidCallback? onPasswordVisibilityChanged;
  final TextDirection? textDirection;

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      onChanged: onChanged,
      keyboardType: keyboardType,
      obscureText: isPassword && !isPasswordVisible,
      textDirection: textDirection,
      decoration: InputDecoration(
        labelText: label,
        suffixIcon: isPassword
            ? IconButton(
                icon: Icon(
                  isPasswordVisible ? Icons.visibility : Icons.visibility_off,
                ),
                onPressed: onPasswordVisibilityChanged,
              )
            : null,
        border: const UnderlineInputBorder(),
        focusedBorder: const UnderlineInputBorder(
          borderSide: BorderSide(color: Colors.teal),
        ),
      ),
    );
  }
}
