// import 'package:flutter/material.dart';
// import 'package:to_do/app_theme.dart';
// import 'package:to_do/generated/l10n.dart';
// import 'package:to_do/home_screen.dart';
// import 'package:to_do/tabs/tasks/default_elevated_button.dart';
// import 'package:to_do/tabs/tasks/default_text_form_field.dart';

// class ResetPasswordScreen extends StatelessWidget {
//   static const String routeName = '/repassword';
//   const ResetPasswordScreen({super.key});

//   @override
//   Widget build(BuildContext context) {
//     final emailController = TextEditingController();
//     final GlobalKey<FormState> formKey = GlobalKey<FormState>();

//     return Scaffold(
//       resizeToAvoidBottomInset: false,
//       extendBodyBehindAppBar: true,
//       appBar: AppBar(
//         title: Center(
//           child: Text(S.of(context).resetPassword),
//         ),
//         backgroundColor: Colors.transparent,
//       ),
//       body: Stack(fit: StackFit.expand, children: [
//         Image.asset(
//           'assets/images/background.png',
//           fit: BoxFit.fill,
//         ),
//         Padding(
//             padding: const EdgeInsets.symmetric(horizontal: 16.0),
//             child: Form(
//                 key: formKey,
//                 child: Column(
//                     mainAxisAlignment: MainAxisAlignment.center,
//                     crossAxisAlignment: CrossAxisAlignment.start,
//                     children: [
//                       Column(
//                         children: [
//                           Center(
//                             child: Icon(
//                               Icons.lock,
//                               color: AppTheme.primaryColor,
//                               size: 60,
//                             ),
//                           ),
//                           Center(
//                             child: Icon(
//                               Icons.password,
//                               size: 40,
//                               color: AppTheme.primaryColor,
//                             ),
//                           )
//                         ],
//                       ),
//                       Center(
//                         child: Text(
//                           S.of(context).pleaseEnterYourEmailAddress,
//                           style: Theme.of(context)
//                               .textTheme
//                               .titleMedium
//                               ?.copyWith(fontSize: 20),
//                         ),
//                       ),
//                       Center(
//                         child: Text(
//                           S.of(context).toResetYourPassword,
//                           style: Theme.of(context)
//                               .textTheme
//                               .titleMedium
//                               ?.copyWith(fontSize: 20),
//                         ),
//                       ),
//                       const SizedBox(
//                         height: 30,
//                       ),
//                       DefaultTextformField(
//                         borderColor: AppTheme.black,
//                         controller: emailController,
//                         hintText: S.of(context).email,
//                         maxlines: 1,
//                         maxlenght: 40,
//                         validator: (value) {
//                           if (value == null || value.isEmpty) {
//                             return 'can not be empty';
//                           }
//                           return null;
//                         },
//                       ),
//                       const SizedBox(
//                         height: 30,
//                       ),
//                       DefaultElevatedButton(
//                         child: Row(
//                           mainAxisAlignment: MainAxisAlignment.spaceAround,
//                           children: [
//                             Text(
//                               S.of(context).next,
//                               style: Theme.of(context).textTheme.titleLarge,
//                             ),
//                           ],
//                         ),
//                         onPressed: () {
//                           if (formKey.currentState!.validate()) {
//                             Navigator.pushReplacementNamed(
//                                 context, HomeScreen.routeName);
//                           }
//                         },
//                       ),
//                     ])))
//       ]),
//     );
//   }
// }
