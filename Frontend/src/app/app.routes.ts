import { Routes } from "@angular/router";
import { HomeComponent } from "./components/home/home.component";
import { LoginFormComponent } from "./components/login-form/login-form.component";
import { SignupFormComponent } from "./components/signup-form/signup-form.component";
import { ProfileComponent } from "./components/profile/profile.component";

export const routes: Routes = [
  { path: "", component: HomeComponent, pathMatch: "full" },
  { path: "login", component: LoginFormComponent, pathMatch: "full" },
  { path: "signup", component: SignupFormComponent, pathMatch: "full" },
  { path: "profile", component: ProfileComponent, pathMatch: "full" },
];
