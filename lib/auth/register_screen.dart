
// import 'package:flutter/material.dart';


// class RegisterScreen extends StatefulWidget {
//   static const String routeName = '/register';
//   const RegisterScreen({super.key});

//   @override
//   State<RegisterScreen> createState() => _RegisterScreenState();
// } 

// class _RegisterScreenState extends State<RegisterScreen> {
//   final emailController = TextEditingController();
//   final passwordController = TextEditingController();
//   final nameController = TextEditingController();
//   final GlobalKey<FormState> formKey = GlobalKey<FormState>();

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       resizeToAvoidBottomInset: false,
//       extendBodyBehindAppBar: true,
//       appBar: AppBar(
//         title: Center(
//           child: Text(S.of(context).createAccount),
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
//               key: formKey,
//               child: Column(
//                   mainAxisAlignment: MainAxisAlignment.center,
//                   crossAxisAlignment: CrossAxisAlignment.start,
//                   children: [
//                     const Spacer(
//                       flex: 15,
//                     ),
//                     DefaultTextformField(
//                       borderColor: AppTheme.black,
//                       controller: nameController,
//                       hintText: S.of(context).firstName,
//                       maxlines: 1,
//                       maxlenght: 40,
//                       validator: (value) {
//                         if (value == null || value.isEmpty) {
//                           return 'can not be empty';
//                         }
//                         return null;
//                       },
//                     ),
//                     const Spacer(
//                       flex: 1,
//                     ),
//                     DefaultTextformField(
//                       borderColor: AppTheme.black,
//                       controller: emailController,
//                       hintText: S.of(context).emailAddress,
//                       maxlines: 1,
//                       maxlenght: 70,
//                       validator: (value) {
//                         if (value == null || value.isEmpty) {
//                           return 'can not be empty';
//                         }
//                         return null;
//                       },
//                     ),
//                     const Spacer(
//                       flex: 1,
//                     ),
//                     DefaultTextformField(
//                       controller: passwordController,
//                       isPasswordVisible: true,
//                       borderColor: AppTheme.black,
//                       hintText: S.of(context).password,
//                       maxlines: 1,
//                       maxlenght: 70,
//                       validator: (value) {
//                         if (value == null || value.isEmpty) {
//                           return 'can not be empty';
//                         }
//                         return null;
//                       },
//                     ),
//                     const Spacer(
//                       flex: 2,
//                     ),
//                     DefaultElevatedButton(
//                       onPressed: _register,
//                       child: Row(
//                         mainAxisAlignment: MainAxisAlignment.spaceAround,
//                         children: [
//                           Text(
//                             S.of(context).createAccount,
//                             style: Theme.of(context).textTheme.titleLarge,
//                           ),
//                           Icon(
//                             Icons.arrow_back,
//                             color: AppTheme.white,
//                           ),
//                         ],
//                       ),
//                     ),
//                     const Spacer(
//                       flex: 1,
//                     ),
//                     Center(
//                       child: TextButton(
//                         onPressed: () {
//                           Navigator.pushReplacementNamed(
//                               context, LoginScreen.routeName);
//                         },
//                         child: Text(
//                           S.of(context).alreadyHaveAnAccount,
//                           style: Theme.of(context)
//                               .textTheme
//                               .bodySmall
//                               ?.copyWith(fontWeight: FontWeight.w900),
//                         ),
//                       ),
//                     ),
//                     const Spacer(flex: 3),
//                   ]),
//             ))
//       ]),
//     );
//   }

//   void _register() {
//     if (formKey.currentState!.validate() == true) {
//       FirebaseUtils.register(
//         name: nameController.text,
//         email: emailController.text,
//         password: passwordController.text,
//       ).then((user) {
//         Provider.of<UserProvider>(context, listen: false).updateUser(user);
//         Navigator.pushReplacementNamed(context, HomeScreen.routeName);
//       }).catchError((onError) {
//         if (onError is FirebaseAuthException && onError.message != null) {
//           Fluttertoast.showToast(
//               msg: onError.message!, toastLength: Toast.LENGTH_SHORT);
//         } else {
//           Fluttertoast.showToast(
//               msg: 'Something went wrong!', toastLength: Toast.LENGTH_SHORT);
//         }
//       });
//     }
//   }
// }
