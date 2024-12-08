import { Component } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { uploadFormData, UploadVideoResponse, VideoService } from '../../services/video/video.service';
import { Router } from '@angular/router';
import { P } from '@angular/cdk/keycodes';

@Component({
  selector: 'app-upload-video',
  standalone: true,
  imports: [HeaderComponent, ReactiveFormsModule],
  templateUrl: './upload-video.component.html',
  styleUrl: './upload-video.component.css'
})
export class UploadVideoComponent {

  public requiredFileType = 'video/mp4';
  public dragCounter = 0;

  public fileErrorMessage = '';

  video: File | null = null;
  public videoUrl: string | ArrayBuffer = '';

  uploadForm = new FormGroup({
    title: new FormControl('', Validators.required),
    description: new FormControl('', Validators.required),
  });

  // construct the component with the injected video service
  public constructor(
    private router: Router,
    private videoService: VideoService
  ) {}

  // get the form group's title control
  public get title() {
    return this.uploadForm.get('title');
  }

  // get the form group's description control
  public get description() {
    return this.uploadForm.get('description');
  }

  // prevent default behavior upon being dragged over
  public onDragOver(event: Event): void {
    event.preventDefault();
  }

  // increment the counter upon drag start
  public onDragEnter(event: Event): void {
    event.preventDefault();

    this.dragCounter++;
  }

  // decrement the counter upon drag leave
  public onDragLeave(event: Event): void {
    this.dragCounter--;
  }

  // upon dropping, get the file that was dropped and call onFileChange with the
  // dropped file
  public onDropSuccess(event: any): void {
    event.preventDefault();
    this.dragCounter = 0;

    const files: FileList = event.dataTransfer.files
    if (!files || files.length === 0) {
      this.fileErrorMessage = 'Not a valid file';
      return;
    }

    this.onFileChange(event.dataTransfer.files);
  }

  // upon selecting a file, get the file and call onFileChange with the
  // selected file
  public onFileSelected(event: Event): void {
    const input: HTMLInputElement = event.currentTarget as HTMLInputElement;
    const files: FileList | null = input.files;
    if (!files || files.length === 0) {
      return;
    }

    this.onFileChange(files);
  }

  // Change the video to the given file. Read data from the file and update
  // videoUrl.
  private onFileChange(files: FileList): void {
    this.video = files.item(0)!;
    if (this.video.size > 500000000) {
      this.fileErrorMessage = 'File is too large';
      return;
    }

    if (!this.requiredFileType.includes(this.video.type)) {
      this.fileErrorMessage = 'File is not an mp4';
      return;
    }

    const reader = new FileReader();
    reader.onload = (event: ProgressEvent<FileReader>) => {
      this.videoUrl = event.target?.result || '';
    };
    reader.readAsDataURL(this.video);
    this.fileErrorMessage = '';
  }

  // upon form submission, make a request to the back-end API with the video and
  // its details (title and description)
  public onSubmit(): void {
    if (this.uploadForm.invalid) {
      this.uploadForm.markAllAsTouched();
      return;
    }

    const uploadData: uploadFormData = {
      video: this.video!,
      title: this.title?.value! ?? '',
      description: this.description?.value! ?? '',
    };

    this.videoService.uploadVideo(uploadData).subscribe({
      next: (video: UploadVideoResponse) => {
        this.router.navigateByUrl(`/video/${video.id}`);
      },
      error: (error: any) => {
        console.log(error);
      }
    });
  }
}
